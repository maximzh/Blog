<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 07.02.16
 * Time: 16:04
 */

namespace AppBundle\Security;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
    const EDIT = 'edit';
    const REMOVE = 'remove';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::REMOVE, self::EDIT))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Comment) {
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
        /** @var Comment $comment */
        $comment = $subject;

        switch ($attribute) {
            case self::REMOVE:
                return $this->canRemove($comment, $user);
            case self::EDIT:
                return $this->canEdit($comment, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canRemove(Comment $comment, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($comment, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(Comment $comment, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        if ($comment->getUser()->getIsAdmin() and !$user->getIsAdmin()) {

            return false;
        }

        return ($user === $comment->getUser()
            or $user->getIsAdmin()
            or $user === $comment->getPost()->getAuthor()
        );


    }


}