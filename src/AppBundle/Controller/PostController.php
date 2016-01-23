<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 11.01.16
 * Time: 20:40
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package AppBundle\Controller
 * @Route("/post")
 */
class PostController extends Controller
{



    /**
     * @param $slug
     * @return array
     * @Route("/{slug}", requirements={"slug" = "^[a-z0-9-]+$"}, name="show_post")
     * @Method("GET")
     * @Template()
     */
    public function showAction($slug)
    {
        $post = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findPostBySlug($slug);

        if (!$post) {
            throw $this->createNotFoundException('No post found'.$slug);
        }

        $comments = $this->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findCommentsByPost($slug);

        $rating = $this->getPostRating($post);



        return [
            'post' => $post,
            'comments' => $comments,
            'rating' => $rating
        ];
    }

    /**
     * @Route("/search", name="search")
     * @Method("POST")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $text = strip_tags(trim($request->get('search_text')));

        if ($text == null or $text == '') {
            return $this->redirectToRoute('homepage');
        }

        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')
            ->searchPosts($text);

        return [
            'posts' => $posts,
            'search_text' => $text
        ];
    }

    public function getPostRating(Post $post)
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
}