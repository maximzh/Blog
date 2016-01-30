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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function showAction(Request $request, $slug)
    {
        $paginationManager = $this->get('app.pagination_manager');
        $pagination = $paginationManager->setLimit(5)->getPostsByAuthor($request, $slug);

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView(
                'AppBundle:Default:postsList.html.twig',
                [
                    'posts' => $pagination['posts'],
                    'nextPageUrl' => $pagination['nextPageUrl'],
                    'nextPage' => $pagination['nextPage'],
                ]
            );

            return new Response($content);
        }

        return [
            'posts' => $pagination['posts'],
            'nextPageUrl' => $pagination['nextPageUrl'],
            'nextPage' => $pagination['nextPage'],

        ];

    }

}