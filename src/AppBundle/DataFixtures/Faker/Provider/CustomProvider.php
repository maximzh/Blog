<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 12.01.16
 * Time: 16:20
 */

namespace AppBundle\DataFixtures\Faker\Provider;


class CustomProvider
{
    public static function createSlug($name)
    {
        $slug = str_replace(' ', '-', $name);
        $slug = preg_replace('/[^A-Za-z\-]/', '', $slug);

        return strtolower($slug);
    }

    public static function image()
    {
        $images = [
            'Cat.jpg',
            'abstraction.jpg',
            'waterfall.jpg',
            'sampler.jpg',
            'Ferrari.jpg',
            'cosmocat.jpg'
        ];

        return $images[array_rand($images)];
    }
}