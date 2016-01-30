<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 30.01.16
 * Time: 12:53
 */

namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Form\CommentType;
use AppBundle\Form\PostType;
use AppBundle\Form\TagType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class FormManager
{
    protected $router;
    protected $formFactory;
    protected $builder;
    protected $doctrine;

    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router, RegistryInterface $doctrine)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->builder = $this->formFactory->createBuilder();
        $this->doctrine = $doctrine;
    }

    public function createNewCommentForm($slug, Comment $comment)
    {
        $em = $this->doctrine->getManager();

        $post = $em->getRepository('AppBundle:Post')
            ->findOneBy(['slug' => $slug]);

        //$comment = new Comment();
        $comment->setPost($post);

        $form = $this->formFactory->create(
            CommentType::class,
            $comment,
            [
                'em' => $em,
                'method' => Request::METHOD_POST,
                'action' => $this->router->generate('new_comment', ['slug' => $slug]),
            ]
        );

        $form
            ->add(
                'save',
                SubmitType::class,
                array(
                    'label' => 'Submit Comment',
                    'attr' => array('class' => "btn btn-primary"),
                )
            );

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

    public function createNewTagForm(Tag $tag)
    {
        return $this->formFactory->create(
            TagType::class,
            $tag,
            array('method' => 'POST',)
        );
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

    public function createCommentDeleteForm(Comment $comment)
    {
        //$builder = $this->formFactory->createBuilder();
        $form = $this->builder->setAction($this->router->generate('remove_comment', array('id' => $comment->getId())))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => ' ', 'attr' => ['class' => 'glyphicon glyphicon-trash btn-link']]
            )
            ->getForm();

        return $form;

    }

    public function createTagDeleteForm(Tag $tag)
    {
        //$builder = $this->formFactory->createBuilder();
        $form = $this->builder->setAction($this->router->generate('remove_tag', array('id' => $tag->getId())))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => ' ', 'attr' => ['class' => 'glyphicon glyphicon-trash btn-link']]
            )
            ->getForm();

        return $form;

    }
}