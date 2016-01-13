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

        $post = $em->getRepository('AppBundle:Post')
            ->findOneBy(['slug' => $slug]);

        $comment = new Comment();
        $comment->setPost($post);

        $form = $this->createForm(
            CommentType::class,
            $comment,
            [
                'em' => $em,
                'method' => Request::METHOD_POST,
                'action' => $this->generateUrl('new_comment', ['slug' => $slug]),
            ]
        );

        $form
            ->add(
                'save',
                SubmitType::class,
                array(
                    'label' => 'Submit Comment',
                    'attr' => array('class' => "btn btn-primary"),
                )
            );

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