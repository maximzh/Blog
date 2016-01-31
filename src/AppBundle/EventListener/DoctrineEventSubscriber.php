<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 31.01.16
 * Time: 18:08
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;


class DoctrineEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'postPersist',
            'postUpdate'
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Tag) {
            $entity->setSlug($this->createSlug($entity->getName()));
        }

        if($entity instanceof Comment) {

            $entity->setCreatedAt(new \DateTime());
        }
        if($entity instanceof Post) {

            $entity->setCreatedAt(new \DateTime());
            $entity->setSlug($this->createSlug($entity->getTitle()));

            if (null !== $entity->getFile()) {
                $filename = sha1(uniqid(mt_rand(), true));
                $path = $filename.'.'.$entity->getFile()->guessExtension();
                $entity->setPath($path);
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Tag) {
            $entity->setSlug($this->createSlug($entity->getName()));
        }

        if($entity instanceof Comment) {
            $entity->setUpdatedAt(new \DateTime());
        }
        if($entity instanceof Post) {
            //$now = new \DateTime();
            $entity->setUpdatedAt(new \DateTime());
            $entity->setSlug($this->createSlug($entity->getTitle()));
            if (null !== $entity->getFile()) {
                $filename = sha1(uniqid(mt_rand(), true));
                $path = $filename.'.'.$entity->getFile()->guessExtension();
                $entity->setPath($path);
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Post) {
            $this->upload($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Post) {
            $this->upload($entity);
        }
    }



    private function upload(Post $post)
    {
        if (null === $post->getFile()) {
            return;
        }

        // check if we have an old image
        $temp = $post->getTemp();
        if (isset($temp)) {
            // delete the old image
            unlink($post->getTemp());
            // clear the temp image path
            $post->setTemp(null);
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        $post->getFile()->move(
            $post->getUploadRootDir(),
            $post->getPath()
        );

        $post->setFile(null);
    }

    private function createSlug($name)
    {
        $slug = str_replace(' ', '-', $name);
        $slug = preg_replace('/[^A-Za-z\-]/', '', $slug);

        return strtolower($slug);
    }
}