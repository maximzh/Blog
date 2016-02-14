<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 11.01.16
 * Time: 20:40
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package AppBundle\Controller
 * @Route("{_locale}/post", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en" })
 */
class PostController extends Controller
{
    /**
     * @Route("/search", name="search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function searchAction(Request $request)
    {
        $result = $this->get('app.search_manager')->search($request);

        return [
            'posts' => $result['posts'],
            'search_text' => $result['search_text'],
        ];
    }

    /**
     * @param $slug
     * @return array
     * @Route("/{slug}", requirements={"slug" = "^[a-z0-9-]+$"}, name="show_post")
     *
     * @Template()
     */
    public function showAction(Request $request, $slug)
    {
        $formManager = $this->get('app.form_manager');
        $data = $this->get('app.pagination_manager')->setFormManager($formManager)->getSinglePostWithComments($request, $slug);

        return $data;
    }



}