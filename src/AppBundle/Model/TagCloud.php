<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 15.01.16
 * Time: 19:15
 */

namespace AppBundle\Model;


use Doctrine\Common\Collections\ArrayCollection;

class TagCloud
{
    public function getCloud($tags)
    {
        $cloud = [];
        $weights = [];
        $maxFont = 30;

        foreach ( $tags as $tag) {
            //$weight = count($tag->getPosts);
            $weights[] = $tag->countPosts();
        }
        sort($weights);
        $minWeight = $weights[0];
        $maxWeight = end($weights);

        foreach ($tags as $tag) {

            $font = $maxFont*($tag->countPosts()-$minWeight)/($maxWeight - $minWeight);
            if ($font < 12) {
                $font = 12;
            }
            $cloud[$tag->getName()]['font'] = ceil($font);
            $cloud[$tag->getName()]['slug'] = $tag->getSlug();
        }

        return $cloud;

    }
}