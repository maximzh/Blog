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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @return array
     * @Route("", name="manage_tags")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tags = $em->getRepository('AppBundle:Tag')
            ->findAllSorted();

        $pager = $this->get('knp_paginator');
        $pagination = $pager->paginate($tags, $request->query->getInt('page', 1), 30);

        $deleteForms = array();

        foreach ($tags as $tag) {
            $deleteForms[$tag->getId()] = $this->createDeleteForm($tag)->createView();
        }

        return [
            'tags' => $pagination,
            'deleteForms' => $deleteForms
        ];
    }

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

    /**
     *
     * @Route("/edit/{id}", name="edit_tag")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Tag $tag)
    {
        $editForm = $this->createForm(TagType::class, $tag);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();
            return $this->redirectToRoute('manage_tags');
        }
        return [
            'tag' => $tag,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * @param Request $request
     * @Route("/remove/{id}", name="remove_tag")
     * @Method("DELETE")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createDeleteForm($tag);

        if ($request->getMethod() == 'DELETE') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->remove($tag);
                $em->flush();


            }
        }

        return $this->redirectToRoute('manage_tags');


    }



    /**
     * @param Tag $tag
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Tag $tag)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('remove_tag', array('id' => $tag->getId())))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => ' ', 'attr' => ['class' => 'glyphicon glyphicon-trash btn-link']]
            )
            ->getForm();
    }

}