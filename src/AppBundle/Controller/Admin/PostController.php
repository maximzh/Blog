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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/post")
 */
class PostController extends Controller
{
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
          PostType::class ,
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
}