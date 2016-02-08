<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 29.01.16
 * Time: 18:48
 */

namespace AppBundle\Service;


use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class PaginationManager
{

    protected $doctrine;
    protected $limit;
    protected $router;
    protected $formManager;

    public function __construct(
        RegistryInterface $doctrine,
        $limit,
        RouterInterface $router
    ) {
        $this->doctrine = $doctrine;
        $this->limit = $limit;
        $this->router = $router;
    }


    public function getPosts(Request $request)
    {
        $currentPage = $request->query->getInt('page', 1);
        $repository = $this->doctrine->getManager()->getRepository('AppBundle:Post');
        $count = $repository->countAllPosts();

        $nextPage = $count > $this->limit * $currentPage
            ? $currentPage + 1
            : false;

        $posts = $repository->findAllPostsWithDependencies($currentPage, $this->limit);

        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->router->generate('homepage', ['page' => $nextPage])
            : false;

        $pagination['posts'] = $posts;
        $pagination['nextPageUrl'] = $nextPageUrl;
        $pagination['nextPage'] = $nextPage;

        return $pagination;
    }


    public function getPostsWithDeleteForms(Request $request, User $user)
    {
        $currentPage = $request->query->getInt('page', 1);
        $repository = $this->doctrine->getManager()->getRepository('AppBundle:Post');
        $count = $repository->countAllPosts();

        $nextPage = $count > $this->limit * $currentPage
            ? $currentPage + 1
            : false;

        if ($user->getIsAdmin()) {
            $posts = $repository->findAllPostsWithDependencies($currentPage, $this->limit);
        } else {
            $posts = $repository->findAllUserPostsWithDependencies($currentPage, $this->limit, $user);
        }


        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->router->generate('manage_posts', ['page' => $nextPage])
            : false;

        $deleteForms = [];
        if ($this->formManager) {

            foreach ($posts as $post) {
                $deleteForms[$post->getId()] = $this->formManager->createPostDeleteForm($post)->createView();
            }
        }

        $pagination['posts'] = $posts;
        $pagination['nextPageUrl'] = $nextPageUrl;
        $pagination['nextPage'] = $nextPage;
        $pagination['deleteForms'] = $deleteForms;

        return $pagination;
    }


    public function getSinglePostWithComments($slug)
    {
        $post = $this->doctrine
            ->getRepository('AppBundle:Post')
            ->findPostBySlug($slug);

        $comments = $this->doctrine
            ->getRepository('AppBundle:Comment')
            ->findCommentsByPost($slug);

        $deleteForms = [];
        if ($this->formManager) {

            foreach ($comments as $comment) {
                $deleteForms[$comment->getId()] = $this->formManager->createPostCommentDeleteForm($comment)->createView(
                );
            }
        }


        return [
            'post' => $post,
            'comments' => $comments,
            'deleteForms' => $deleteForms,
        ];
    }


    public function getUserWithPosts($slug)
    {
        $userWithPosts = $this->doctrine
            ->getRepository('AppBundle:User')
            ->findUserWithDependencies($slug);

        return $userWithPosts;
    }


    public function getPostsByUser(Request $request, $slug)
    {
        $author = $this->doctrine->getRepository('AppBundle:User')
            ->findUserWithDependencies($slug);

        if (!$author) {
            throw new NotFoundHttpException('author not found: '.$slug);
        }

        $currentPage = $request->query->getInt('page', 1);
        $repository = $this->doctrine->getManager()->getRepository('AppBundle:Post');
        $count = $repository->countAllAuthorPosts($slug);

        $nextPage = $count > $this->limit * $currentPage
            ? $currentPage + 1
            : false;

        $posts = $repository->findPostsByAuthor($slug, $currentPage, $this->limit);

        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->router->generate('show_user_posts', ['slug' => $slug, 'page' => $nextPage])
            : false;

        $pagination['posts'] = $posts;
        $pagination['nextPageUrl'] = $nextPageUrl;
        $pagination['nextPage'] = $nextPage;
        $pagination['count'] = $count;

        return $pagination;
    }


    /**
     * @param Request $request
     * @param $slug
     * @return mixed
     */
    public function getPostsByTag(Request $request, $slug)
    {
        $tag = $this->doctrine->getManager()->getRepository('AppBundle:Tag')->findTagWithPosts($slug);

        if (!$tag) {
            throw new NotFoundHttpException('tag not found: '.$slug);
        }

        $currentPage = $request->query->getInt('page', 1);
        $repository = $this->doctrine->getManager()->getRepository('AppBundle:Post');

        $count = count($tag->getPosts());

        $nextPage = $count > $this->limit * $currentPage
            ? $currentPage + 1
            : false;

        $posts = $repository->findPostsByTag($slug, $currentPage, $this->limit);

        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->router->generate('show_tag', ['slug' => $slug, 'page' => $nextPage])
            : false;

        $pagination['posts'] = $posts;
        $pagination['nextPageUrl'] = $nextPageUrl;
        $pagination['nextPage'] = $nextPage;
        $pagination['tag'] = $tag->getName();
        $pagination['count'] = $count;

        return $pagination;
    }

    public function getCommentsWithDeleteForms(Request $request, User $user)
    {
        $currentPage = $request->query->getInt('page', 1);
        $repository = $this->doctrine->getManager()->getRepository('AppBundle:Comment');
        $count = $repository->countAllComments();

        $nextPage = $count > $this->limit * $currentPage
            ? $currentPage + 1
            : false;

        if ($user->getIsAdmin()) {
            $comments = $repository->findAllCommentsWithDependencies($currentPage, $this->limit);
        } else {
            $comments = $repository->findAllCommentsByUserAndUserPosts($currentPage, $this->limit, $user);
        }


        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->router->generate('manage_comments', ['page' => $nextPage])
            : false;

        $deleteForms = [];
        foreach ($comments as $comment) {
            $deleteForms[$comment->getId()] = $this->formManager->createCommentDeleteForm($comment)->createView();
        }
        $pagination['comments'] = $comments;
        $pagination['nextPageUrl'] = $nextPageUrl;
        $pagination['nextPage'] = $nextPage;
        $pagination['deleteForms'] = $deleteForms;

        return $pagination;
    }

    public function getUserCommentsWithDeleteForms(Request $request, User $user, User $admin)
    {
        $currentPage = $request->query->getInt('page', 1);
        $repository = $this->doctrine->getManager()->getRepository('AppBundle:Comment');
        $count = $repository->countAllComments();

        $nextPage = $count > $this->limit * $currentPage
            ? $currentPage + 1
            : false;

        $comments = $repository->findAllCommentsByUserInAdminPosts($currentPage, $this->limit, $user, $admin);


        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->router->generate(
                'manage_user_comments',
                ['id' => $user->getId(), 'page' => $nextPage]
            )
            : false;

        $deleteForms = [];
        foreach ($comments as $comment) {
            $deleteForms[$comment->getId()] = $this->formManager->createCommentDeleteForm($comment)->createView();
        }
        $pagination['comments'] = $comments;
        $pagination['nextPageUrl'] = $nextPageUrl;
        $pagination['nextPage'] = $nextPage;
        $pagination['deleteForms'] = $deleteForms;

        return $pagination;
    }

    public function getTagsWithDeleteForms(Request $request)
    {
        $currentPage = $request->query->getInt('page', 1);
        $repository = $this->doctrine->getManager()->getRepository('AppBundle:Tag');
        $count = $repository->countAllTags();

        $nextPage = $count > $this->limit * $currentPage
            ? $currentPage + 1
            : false;

        $tags = $repository->findAllTags($currentPage, $this->limit);

        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->router->generate('manage_tags', ['page' => $nextPage])
            : false;

        $deleteForms = [];
        foreach ($tags as $tag) {
            $deleteForms[$tag->getId()] = $this->formManager->createTagDeleteForm($tag)->createView();
        }
        $pagination['tags'] = $tags;
        $pagination['nextPageUrl'] = $nextPageUrl;
        $pagination['nextPage'] = $nextPage;
        $pagination['deleteForms'] = $deleteForms;

        return $pagination;
    }


    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }


    public function setFormManager(FormManager $manager)
    {
        $this->formManager = $manager;

        return $this;
    }

}