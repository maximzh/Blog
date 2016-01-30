<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 30.01.16
 * Time: 12:53
 */

namespace AppBundle\Service;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Component\Form;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

class FormManager
{
    protected $router;
    protected $formFactory;
    protected $builder;

    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->builder = $this->formFactory->createBuilder();
    }

    public function createPostDeleteForm(Post $post)
    {
        //$builder = $this->formFactory->createBuilder();
        $form = $this->builder->setAction($this->router->generate('remove_post', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => ' ', 'attr' => ['class' => 'glyphicon glyphicon-trash btn-link']]
            )
            ->getForm();

        return $form;

    }

    public function createNewPostForm(Post $post)
    {
        return $this->formFactory->create(
            PostType::class,
            $post,
            array('method' => 'POST',)
        );
    }
}