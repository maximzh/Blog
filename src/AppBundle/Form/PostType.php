<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 14.01.16
 * Time: 16:57
 */

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options); // TODO: Change the autogenerated stub
        $builder
            ->add('title', TextType::class)
            ->add('text', TextareaType::class)
            ->add('file', FileType::class,[
                'required' => false,
                'data_class' => null,
                'mapped' => true,
                'label' => 'Select image to upload'
            ])
            ->add('tags', EntityType::class, array(
                'class'   => 'AppBundle\Entity\Tag',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label_attr' => [
                    'class' => 'checkbox-inline'
                ]
            ))
            ->add(
                'author',
                EntityType::class,
                array(
                    'class' => 'AppBundle:Author',
                    'choice_label' => 'name',
                )
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Post',
                'em' => null,
            )
        );
    }

    public function getBlockPrefix()
    {
        return "post";
    }
}