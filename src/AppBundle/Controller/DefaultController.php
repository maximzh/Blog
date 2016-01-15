<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Model\TagCloud;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
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


        return [
            'posts' => $posts,
            'last_comments' => $lastComments,
            'tag_cloud' => $tagCloud,
            'tags' => $tags
        ];
    }
}
