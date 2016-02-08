<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 13.01.16
 * Time: 16:16
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{
    /**
     * @param Request $request
     * @param $slug
     * @Method("POST")
     * @Route("/{slug}/comment/new", name="new_comment")
     * @Template("AppBundle:Comment:comment_form.html.twig")
     * @return array
     */
    public function newAction(Request $request, $slug)
    {

        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $form = $this->get('app.form_manager')->createNewCommentForm($slug, $comment);

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $user = $this->getUser();
                $comment->setUser($user);
                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('show_post', ['slug' => $slug]);
            }
        }


        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @Route("/comment/remove/{id}", name="remove_post_comment")
     * @Method("DELETE")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, Comment $comment)
    {
        $em = $this->getDoctrine()->getManager();
        $formManager = $this->get('app.form_manager');
        $form = $formManager->createPostCommentDeleteForm($comment);

        if ($request->getMethod() == 'DELETE') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->remove($comment);
                $em->flush();


            }
        }

        return $this->redirectToRoute('show_post', ['slug' => $comment->getPost()->getSlug()]);


    }

    /**
     *
     * @Route("/comment/edit/{id}", name="edit_post_comment")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Comment $comment)
    {
        $editForm = $this->createForm(CommentType::class, $comment);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('show_post', ['slug' => $comment->getPost()->getSlug()]);
        }

        return [
            'comment' => $comment,
            'edit_form' => $editForm->createView(),
        ];
    }
}