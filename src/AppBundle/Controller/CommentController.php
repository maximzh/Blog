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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('show_post', ['slug' => $slug]);
            }
        }


        return [
            'form' => $form->createView(),
        ];
    }
}