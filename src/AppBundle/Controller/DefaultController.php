<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array|Response
     */
    public function indexAction(Request $request)
    {
        $paginationManager = $this->get('app.pagination_manager');
        $pagination = $paginationManager->setLimit(5)->getPosts($request);

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
