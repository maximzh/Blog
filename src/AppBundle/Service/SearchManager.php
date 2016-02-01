<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 30.01.16
 * Time: 14:05
 */

namespace AppBundle\Service;


use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class SearchManager
{

    public function __construct(
        RegistryInterface $doctrine,
        RouterInterface $router
    ) {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    public function search(Request $request)
    {
        $text = strip_tags(trim($request->get('search_text')));

        if ($text == null or $text == '') {
            return ;
        }

        $posts = $this->doctrine->getRepository('AppBundle:Post')
            ->searchPosts($text);

        return [
            'posts' => $posts,
            'search_text' => $text,
        ];
    }
}