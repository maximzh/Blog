<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 30.01.16
 * Time: 14:05
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class SearchManager
{
    protected  $doctrine;
    protected $router;
    protected $formManager = null;

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
            return;
        }

        $posts = $this->doctrine->getRepository('AppBundle:Post')
            ->searchPosts($text);

        return [
            'posts' => $posts,
            'search_text' => $text,
        ];
    }

    public function adminPostSearch(Request $request, User $user)
    {
        $text = strip_tags(trim($request->get('search_text')));
        $repository = $this->doctrine->getRepository('AppBundle:Post');

        if ($text == null or $text == '') {
            return;
        }

        $deleteForms = [];

        if ($user->getIsAdmin()) {

            $posts = $repository->searchPosts($text);
            if (!$posts) {

                return;
            }
        }

        if ($user->getIsModerator()) {

            $posts = $repository->searchModeratorPosts($text, $user);
            if (!$posts) {

                return;
            }
        }


            if ($this->formManager) {

                foreach ($posts as $post) {
                    $deleteForms[$post->getId()] = $this->formManager->createPostDeleteForm($post)->createView();
                }

                return [
                    'posts' => $posts,
                    'deleteForms' => $deleteForms,
                ];

        }



    }

    public function setFormManager(FormManager $manager)
    {
        $this->formManager = $manager;

        return $this;
    }
}