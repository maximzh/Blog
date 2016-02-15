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
     * @Route("{_locale}/comment/remove/{id}", name="remove_post_comment", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en" })
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
     * @Route("{_locale}/comment/edit/{id}", name="edit_post_comment")
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