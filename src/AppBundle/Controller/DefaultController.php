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
        $limit = 4;
        $currentPage = $request->query->getInt('page', 1);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Post');

        $count = $repository->countAllPosts();

        $nextPage = $count > $limit * $currentPage
            ? $currentPage + 1
            : false;

        $posts = $repository->findAllPostsWithDependencies($currentPage, $limit);

        $nextPageUrl = $nextPage
            ? $nextPageUrl = $this->generateUrl('homepage', ['page' => $nextPage])
            : false;
        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView(
                'AppBundle:Default:postsList.html.twig',
                ['posts' => $posts, 'nextPageUrl' => $nextPageUrl, 'nextPage' => $nextPage]
            );

            return new Response($content);
        }


        $lastComments = $this->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findLastComments();

        $tags = $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->findAllTagsWithDependencies();

        $topPosts = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findTopPosts();

        return [
            'posts' => $posts,
            'last_comments' => $lastComments,
            'tags' => $tags,
            'top_posts' => $topPosts,
            'nextPageUrl' => $nextPageUrl,
            'nextPage' => $nextPage,

        ];

    }


}
