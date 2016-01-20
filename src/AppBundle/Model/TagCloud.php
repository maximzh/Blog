<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 15.01.16
 * Time: 19:15
 */

namespace AppBundle\Model;


class TagCloud
{
    public function getCloud($tags)
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