<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Model\TagCloud;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $limit = 4;

        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')
            ->findAllPostsWithDependencies();
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            $limit
        );

        $lastComments = $this->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findLastComments();

        $tags = $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->findAllTagsWithDependencies();

        $cloud = new TagCloud();
        $tagCloud = $cloud->getCloud($tags);

        $topPosts = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findTopPosts();

        return [
            'posts' => $pagination,
            'last_comments' => $lastComments,
            'tag_cloud' => $tagCloud,
            'tags' => $tags,
            'top_posts' => $topPosts
        ];

        /*
        $posts = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findAllPostsWithDependencies();

        if (!$posts) {

            throw $this->createNotFoundException('No posts found');
        }

        $lastComments = $this->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findLastComments();

        $tags = $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->findAllTagsWithDependencies();

        $cloud = new TagCloud();
        $tagCloud = $cloud->getCloud($tags);

        $topPosts = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findTopPosts();


        return [
            'posts' => $posts,
            'last_comments' => $lastComments,
            'tag_cloud' => $tagCloud,
            'tags' => $tags,
            'top_posts' => $topPosts
        ];
        */
    }
}
