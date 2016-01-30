<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 29.01.16
 * Time: 18:48
 */

namespace AppBundle\Service;


use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form;

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

    public function getPostsWithDeleteForms(Request $request)
    {
        $currentPage = $request->query->getInt('page', 1);
        $repository = $this->doctrine->getManager()->getRepository('AppBundle:Post');
        $count = $repository->countAllPosts();

        $nextPage = $count > $this->limit * $currentPage
            ? $currentPage + 1
            : false;

        $posts = $repository->findAllPostsWithDependencies($currentPage, $this->limit);

        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->router->generate('manage_posts', ['page' => $nextPage])
            : false;

        $deleteForms =[];
        foreach($posts as $post) {
            $deleteForms[$post->getId()] = $this->formManager->createPostDeleteForm($post)->createView();
        }
        $pagination['posts'] = $posts;
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