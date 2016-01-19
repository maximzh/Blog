<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 16.01.16
 * Time: 17:26
 */

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Tag;
use AppBundle\Form\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/tag")
 */
class TagController extends Controller
{
    /**
     * @param Request $request
     * @Route("/new", name="new_tag")
     * @Template()
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm(
            TagType::class,
            $tag,
            array(
                'method' => 'POST',
            )
        );
        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($tag);
                $em->flush();

                return $this->redirectToRoute('admin_default');
            }
        }

        return [
            'tag' => $tag,
            'form' => $form->createView(),
        ];
    }

}