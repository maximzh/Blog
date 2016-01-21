<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 22.01.16
 * Time: 0:56
 */

namespace AppBundle\Tests\Form;

use AppBundle\Form\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Entity\Comment;


class CommentTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'rating' => '3',
            'text' => 'Some text',
            'commentAuthor' => 'Some Author'
        );
        $form = $this->factory->create(CommentType::class);
        $object = new Comment();
        $object->setRating(3);
        $object->setText('Some text');
        $object->setCommentAuthor('Some Author');
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());
        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}