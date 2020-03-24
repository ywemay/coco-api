<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    // these strings are just invented: you can use anything
    const LIST = 'list';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on SaleOrder objects inside this voter
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @param  String         $attribute [description]
     * @param  User         $subject   [description]
     * @param  TokenInterface $token     [description]
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
          return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::LIST:
                return $this->canList();
            case self::VIEW:
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canList()
    {
      return $this->security->isGranted('ROLE_ADMIN')
        || $this->security->isGranted('ROLE_TEAMLEADER');
    }

    private function canEdit(User $subject, User $user)
    {
        return $this->isTheSameUser($subject, $user);
    }

    private function canDelete(User $subject, User $user)
    {
        return false;
    }

    private function isTheSameUser(User $subject, User $user)
    {
        // Users can edit thier accounts only and not allowed to change their usernames
        return $user->getId() === $subject->getId() && $user->getUsername() === $subject->getUsername();
    }

}
