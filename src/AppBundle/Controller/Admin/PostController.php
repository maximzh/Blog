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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/post")
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

        $paginationManager = $this->container->get('app.pagination_manager');
        $pagination = $paginationManager->setLimit(10)->getPostsWithDeleteForms($request);
/*
        $limit = 15;
        $currentPage = $request->query->getInt('page', 1);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Post');

        $count = $repository->countAllPosts();

        $nextPage = $count > $limit * $currentPage
            ? $currentPage + 1
            : false;

        $deleteForms = [];
        $posts = $repository->findAllPostsWithDependencies($currentPage, $limit);

        foreach ($posts as $post) {
            $deleteForms[$post->getId()] = $this->createDeleteForm($post)->createView();
        }

        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->generateUrl('manage_posts', ['page' => $nextPage])
            : false;
*/
        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView(
                'AppBundle:Admin/Post:postsList.html.twig',
                [
                    'posts' => $pagination['posts'],
                    'nextPageUrl' => $pagination['nextPageUrl'],
                    'nextPage' => $pagination['nextPage'],
                    'deleteForms' => $pagination['deleteForms'],
                    //'posts' => $posts,
                    //'nextPage' =>$nextPage,
                    //'nextPageUrl' => $nextPageUrl,
                    //'deleteForms' => $deleteForms,
                ]
            );

            return new Response($content);
        }


        return [
            'posts' => $pagination['posts'],
            'nextPageUrl' => $pagination['nextPageUrl'],
            'nextPage' => $pagination['nextPage'],
            'deleteForms' => $pagination['deleteForms'],
            //'posts' => $posts,
            //'nextPage' =>$nextPage,
            //'nextPageUrl' => $nextPageUrl,
            //'deleteForms' => $deleteForms,

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

        $form = $this->createForm(
            PostType::class,
            $post,
            array('method' => 'POST',)
        );

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isValid()) {

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
        $form = $this->get('app.pagination_manager')->createDeleteForm($post);

        if ($request->getMethod() == 'DELETE') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->remove($post);
                $em->flush();


            }
        }

        return $this->redirectToRoute('manage_posts');

        //return ['form' => $form->createView()];
    }

    /**
     * @param Post $post
     * @return \Symfony\Component\Form\Form
     */

    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('remove_post', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => ' ', 'attr' => ['class' => 'glyphicon glyphicon-trash btn-link']]
            )
            ->getForm();
    }

}