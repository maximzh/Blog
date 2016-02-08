<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 07.02.16
 * Time: 18:44
 */

namespace AppBundle\Security;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class PostVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const REMOVE = 'remove';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::REMOVE, self::EDIT, self::VIEW))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Post $post */
        $post = $subject;

        switch ($attribute) {
            case self::REMOVE:
                return $this->canRemove($post, $user);
            case self::EDIT:
                return $this->canEdit($post, $user);
            case self::VIEW:
                return $this->canView($post, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canRemove(Post $post, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($post, $user)) {
            return true;
        }

        return false;
    }

    private function canView(Post $post, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($post, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(Post $post, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        //if ($post->getAuthor()->getIsAdmin() and !$user->getIsAdmin()) {

        //    return false;
        //}

        return ($user === $post->getAuthor()
            or $user->getIsAdmin()
            //or $user === $comment->getPost()->getAuthor()
        );


    }
}