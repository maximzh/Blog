<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 27.01.16
 * Time: 14:27
 */

namespace AppBundle\Twig;


use AppBundle\Entity\Post;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AppExtension extends \Twig_Extension
{
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'postRating',
                array($this, 'getRating'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'tagCloud',
                array($this, 'getTagCloud'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'lastComments',
                array($this, 'getLastComments'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'topPosts',
                array($this, 'getTopPosts'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'allTags',
                array($this, 'getAllTags'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'countAllComments',
                array($this, 'countAllComments'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'countAllTags',
                array($this, 'countAllTags'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'countAllPosts',
                array($this, 'countAllPosts'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
            new \Twig_SimpleFunction(
                'countCommentsWithRating',
                array($this, 'countCommentsWithRating'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function getRating(\Twig_Environment $twig, Post $post)
    {
        $comments = $post->getComments();
        $rating = 0;
        $countCommentsWithRating = 0;

        if (count($comments) !== 0) {

            foreach ($comments as $comment) {
                if (0 !== $comment->getRating()) {
                    $countCommentsWithRating++;
                    $rating = $rating + $comment->getRating();
                }
            }

            if ($countCommentsWithRating !== 0) {
                $rating = $rating / $countCommentsWithRating;
            }

        }


        return $rating;
    }

    public function countCommentsWithRating(\Twig_Environment $twig, Post $post)
    {
        $comments = $post->getComments();
        $countCommentsWithRating = 0;
        if (count($comments) !== 0) {

            foreach ($comments as $comment) {
                if (0 !== $comment->getRating()) {
                    $countCommentsWithRating++;
                }
            }
        }

        return $countCommentsWithRating;
    }

    public function getTagCloud(\Twig_Environment $twig, $tags)
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

            $color = '#FF6600';
            $font = ($maxFont * ($tag->countPosts() - $minWeight) / ($maxWeight - $minWeight)) + 14;
            $cloud[$tag->getName()]['font'] = ceil($font);
            $cloud[$tag->getName()]['slug'] = $tag->getSlug();

            if ($cloud[$tag->getName()]['font'] > 15 && $cloud[$tag->getName()]['font'] <= 20) {
                $color = '#0639A4';
            }

            if ($cloud[$tag->getName()]['font'] > 20 && $cloud[$tag->getName()]['font'] <= 33) {
                $color = '#53ACED';
            }

            if ($cloud[$tag->getName()]['font'] > 33) {
                $color = '#7ABA20';
            }
            $cloud[$tag->getName()]['color'] = $color;

        }

        return $cloud;
    }

    public function getLastComments(\Twig_Environment $twig)
    {

        return $this->doctrine->getManager()
            ->getRepository('AppBundle:Comment')
            ->findLastComments();
    }

    public function getTopPosts(\Twig_Environment $twig)
    {
        return $this->doctrine->getManager()
            ->getRepository('AppBundle:Post')
            ->findTopPosts();
    }

    public function getAllTags(\Twig_Environment $twig)
    {
        return $this->doctrine->getManager()
            ->getRepository('AppBundle:Tag')
            ->findAllTagsWithDependencies();

    }

    public function countAllComments()
    {
        return $this->doctrine->getManager()
            ->getRepository('AppBundle:Comment')
            ->countAllComments();
    }

    public function countAllTags()
    {
        return $this->doctrine->getManager()
            ->getRepository('AppBundle:Tag')
            ->countAllTags();
    }

    public function countAllPosts()
    {
        return $this->doctrine->getManager()
            ->getRepository('AppBundle:Post')
            ->countAllPosts();
    }



    public function getName()
    {
        return 'app_extension';
    }
}