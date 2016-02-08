<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 08.02.16
 * Time: 20:43
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class UserController
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/{slug}", requirements={"slug" = "^[a-zA-Z0-9-]+$"},name="show_user_posts")
     * @Template()
     */
    public function showAction(Request $request, $slug)
    {
        $paginationManager = $this->get('app.pagination_manager');
        $pagination = $paginationManager->setLimit(5)->getPostsByUser($request, $slug);

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
            'count' => $pagination['count'],

        ];

    }
}