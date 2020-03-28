<?php
namespace App\Security;

use App\Entity\CustomerProfile;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CustomerProfileVoter extends Voter
{
    // these strings are just invented: you can use anything
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
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof CustomerProfile) {
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

        if (!$this->security->isGranted('ROLE_CUSTOMER')) {
          return false;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(CustomerProfile $subject, User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
          return true;
        }
        return in_array($subject->getStaff(), $user);
    }

    private function canDelete(CustomerProfile $subject, User $user)
    {
        if ($subject->getSaleOrders()) {
          return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
          return true;
        }
        return in_array($subject->getStaff(), $user);
    }
}
