<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 20.01.16
 * Time: 15:21
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuthorController
 * @package AppBundle\Controller
 * @Route("/author")
 */
class AuthorController extends Controller
{

    /**
     * @Route("/{slug}", requirements={"slug" = "^[a-zA-z-]+$"},name="show_author_posts")
     * @Template()
     */
    public function showAction($slug)
    {
        $author = $this->getDoctrine()
            ->getRepository('AppBundle:Author')
            ->findAuthorWithDependencies($slug);

        if (!$author) {

            throw $this->createNotFoundException('Author not found: '.$slug);
        }

        return ['author' => $author];
    }
}