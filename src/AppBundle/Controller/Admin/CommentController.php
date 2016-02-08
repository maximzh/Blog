<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 21.01.16
 * Time: 18:29
 */

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $user = $this->getUser();

        $paginationManager = $this->get('app.pagination_manager');
        $formManager = $this->get('app.form_manager');
        $pagination = $paginationManager->setLimit(10)->setFormManager($formManager)->getCommentsWithDeleteForms(
            $request,
            $user
        );

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView(
                'AppBundle:Admin/Comment:commentList.html.twig',
                [
                    'comments' => $pagination['comments'],
                    'nextPageUrl' => $pagination['nextPageUrl'],
                    'nextPage' => $pagination['nextPage'],
                    'deleteForms' => $pagination['deleteForms'],
                ]
            );

            return new Response($content);
        }


        return [
            'comments' => $pagination['comments'],
            'nextPageUrl' => $pagination['nextPageUrl'],
            'nextPage' => $pagination['nextPage'],
            'deleteForms' => $pagination['deleteForms'],
        ];
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     * @Route("/admin/comment/user/{id}", name="manage_user_comments")
     * @Template("@App/Admin/Comment/userComments.html.twig")
     */
    public function showUserComments(Request $request, User $user)
    {

        $admin = $this->getUser();
        $paginationManager = $this->get('app.pagination_manager');
        $formManager = $this->get('app.form_manager');
        $pagination = $paginationManager->setLimit(10)->setFormManager($formManager)->getUserCommentsWithDeleteForms(
            $request,
            $user,
            $admin
        );

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView(
                'AppBundle:Admin/Comment:commentList.html.twig',
                [
                    'comments' => $pagination['comments'],
                    'nextPageUrl' => $pagination['nextPageUrl'],
                    'nextPage' => $pagination['nextPage'],
                    'deleteForms' => $pagination['deleteForms'],
                ]
            );

            return new Response($content);
        }


        return [
            'comments' => $pagination['comments'],
            'nextPageUrl' => $pagination['nextPageUrl'],
            'nextPage' => $pagination['nextPage'],
            'deleteForms' => $pagination['deleteForms'],
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
        $formManager = $this->get('app.form_manager');
        $form = $formManager->createCommentDeleteForm($comment);
        //$form = $this->createDeleteForm($comment);

        if ($request->getMethod() == 'DELETE') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->remove($comment);
                $em->flush();
            }
        }

        return $this->redirectToRoute('manage_comments');
    }

}