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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagController
 * @package AppBundle\Controller
 * @Route("{_locale}/tag", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en" })
 */
class TagController extends Controller
{
    /**
     * @Route("/{slug}", requirements={"slug" = "^[a-zA-z0-9]+$"},name="show_tag")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Request $request, $slug)
    {
        $paginationManager = $this->get('app.pagination_manager');
        $pagination = $paginationManager->setLimit(5)->getPostsByTag($request, $slug);

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
            'tag' => $pagination['tag'],
            'count' => $pagination['count'],

        ];

    }
}