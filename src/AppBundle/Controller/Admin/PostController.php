<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 15.01.16
 * Time: 11:44
 */

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/post")
 * @Security("has_role('ROLE_MODERATOR')")
 */
class PostController extends Controller
{
    /**
     * @Route("", name="manage_posts")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $paginationManager = $this->get('app.pagination_manager');
        $formManager = $this->get('app.form_manager');
        $pagination = $paginationManager->setLimit(10)->setFormManager($formManager)->getPostsWithDeleteForms(
            $request,
            $user
        );

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView(
                'AppBundle:Admin/Post:postsList.html.twig',
                [
                    'posts' => $pagination['posts'],
                    'nextPageUrl' => $pagination['nextPageUrl'],
                    'nextPage' => $pagination['nextPage'],
                    'deleteForms' => $pagination['deleteForms'],
                ]
            );

            return new Response($content);
        }


        return [
            'posts' => $pagination['posts'],
            'nextPageUrl' => $pagination['nextPageUrl'],
            'nextPage' => $pagination['nextPage'],
            'deleteForms' => $pagination['deleteForms'],
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/new", name="new_post")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();
        $form = $this->get('app.form_manager')->createNewPostForm($post);

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $user = $this->getUser();
                $post->setAuthor($user);
                $em->persist($post);
                $em->flush();

                return $this->redirectToRoute('show_post', ['slug' => $post->getSlug()]);
            }
        }

        return [
            'post' => $post,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/edit/{id}", name="edit_post")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Post $post, Request $request)
    {

        $editForm = $this->createForm(PostType::class, $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('manage_posts');
        }

        return [
            'post' => $post,
            'form' => $editForm->createView(),
        ];
    }

    /**
     * @param Request $request
     * @Route("/remove/{id}", name="remove_post")
     * @Method("DELETE")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('app.form_manager')->createPostDeleteForm($post);

        if ($request->getMethod() == 'DELETE') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->remove($post);
                $em->flush();


            }
        }

        return $this->redirectToRoute('manage_posts');

    }

    /**
     * @Route("/search", name="admin_post_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request$request)
    {
        $formManager = $this->get('app.form_manager');
        $user = $this->getUser();
        $searchResult = $this->get('app.search_manager')->setFormManager($formManager)->adminPostSearch($request, $user);

        return [
            'posts' => $searchResult['posts'],
            'deleteForms' => $searchResult['deleteForms'],
        ];
    }

}