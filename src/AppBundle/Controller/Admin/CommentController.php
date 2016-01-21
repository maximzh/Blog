<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 21.01.16
 * Time: 18:29
 */

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{
    /**
     * @param Request $request
     * @return array
     *
     * @Route("/admin/comment", name="manage_comments")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository('AppBundle:Comment')
            ->findAllSorted();

        $pager = $this->get('knp_paginator');
        $pagination = $pager->paginate($comments, $request->query->getInt('page', 1), 30);

        $deleteForms = array();

        foreach ($comments as $comment) {
            $deleteForms[$comment->getId()] = $this->createDeleteForm($comment)->createView();
        }

        return [
            'comments' => $pagination,
            'deleteForms' => $deleteForms
        ];
    }

    /**
     *
     * @Route("/admin/comment/edit/{id}", name="edit_comment")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Comment $comment)
    {
        $editForm = $this->createForm(CommentType::class, $comment);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('manage_comments');
        }
        return [
            'comment' => $comment,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * @param Request $request
     * @Route("/admin/comment/remove/{id}", name="remove_comment")
     * @Method("DELETE")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, Comment $comment)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createDeleteForm($comment);

        if ($request->getMethod() == 'DELETE') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->remove($comment);
                $em->flush();


            }
        }

        return $this->redirectToRoute('manage_comments');


    }



    /**
     * @param Comment $comment
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Comment $comment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('remove_comment', array('id' => $comment->getId())))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => ' ', 'attr' => ['class' => 'glyphicon glyphicon-trash btn-link']]
            )
            ->getForm();
    }
}