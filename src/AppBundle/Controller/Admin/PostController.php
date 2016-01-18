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

        $limit = 25;
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
        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView(
                'AppBundle:Admin/Post:postsList.html.twig',
                [
                    'posts' => $posts,
                    'nextPageUrl' => $nextPageUrl,
                    'nextPage' => $nextPage,
                    'deleteForms' => $deleteForms,
                ]
            );

            return new Response($content);
        }


        return [
            'posts' => $posts,
            'nextPageUrl' => $nextPageUrl,
            'nextPage' => $nextPage,
            'deleteForms' => $deleteForms,

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
            array(
                //'action' => $this->generateUrl('create_post'),
                'method' => 'POST',
            )
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
     * @param $slug
     * @param Request $request
     * @Route("/edit/{slug}", name="edit_post")
     * @Template()
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction($slug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('AppBundle:Post')
            ->findOneBy(['slug' => $slug]);

        $form = $this->createForm(
            PostType::class,
            $post,
            [
                'em' => $em,
                'action' => $this->generateUrl('edit_post', ['slug' => $slug]),
                'method' => Request::METHOD_POST,
            ]
        );

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($post);
                $em->flush();

                return $this->redirectToRoute('manage_posts');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @Route("/remove/{id}", name="remove_post")
     * @Template()
     * @Method("DELETE")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createDeleteForm($post);

        /*
        $form = $this->createForm(
            PostType::class,
            $post,
            [
                'em' => $em,
                'action' => $this->generateUrl('remove_post', ['slug' => $slug]),
                'method' => Request::METHOD_DELETE,
            ]
        );
        */


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