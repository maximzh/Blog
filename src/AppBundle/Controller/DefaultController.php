<?php

namespace AppBundle\Controller;

use AppBundle\Model\TagCloud;
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

        //$cloud = new TagCloud();
        $tagCloud = $this->getTagCloud($tags);

        $topPosts = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findTopPosts();

        return [
            'posts' => $posts,
            'last_comments' => $lastComments,
            'tag_cloud' => $tagCloud,
            'tags' => $tags,
            'top_posts' => $topPosts,
            'nextPageUrl' => $nextPageUrl,
            'nextPage' => $nextPage,

        ];

    }

    public function getTagCloud($tags)
    {
        $cloud = [];
        $weights = [];
        $maxFont = 25;

        foreach ($tags as $tag) {

            $weights[] = $tag->countPosts();
        }
        sort($weights);
        $minWeight = $weights[0];
        $maxWeight = end($weights);

        foreach ($tags as $tag) {

            $font = ($maxFont * ($tag->countPosts() - $minWeight) / ($maxWeight - $minWeight)) + 10;
            $cloud[$tag->getName()]['font'] = ceil($font);
            $cloud[$tag->getName()]['slug'] = $tag->getSlug();
        }

        return $cloud;

    }
}
