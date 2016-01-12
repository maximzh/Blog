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

    public static function preview()
    {
        $previews = [
            '/images/akai_preview.jpg',
            '/images/korg_electribe_preview.jpg',
            '/images/korg_preview.png',
            '/images/padkontrol_preview.jpg',
        ];

        return $previews[array_rand($previews)];
    }
}