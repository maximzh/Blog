<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 15.01.16
 * Time: 20:26
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagController
 * @package AppBundle\Controller
 * @Route("/tag")
 */
class TagController extends Controller
{
    /**
     * @Route("/{slug}", requirements={"slug" = "^[a-zA-z0-9]+$"},name="show_tag")
     * @Method("GET")
     * @Template()
     */
    public function showAction($slug)
    {
        $tag = $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->findTagWithPosts($slug);

        if (!$tag) {
             throw $this->createNotFoundException('No tag found: '.$slug);
         }

        return ['tag' => $tag];
    }
}