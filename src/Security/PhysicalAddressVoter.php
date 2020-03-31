<?php
namespace App\Security;

use App\Entity\PhysicalAddress;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PhysicalAddressVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof PhysicalAddress) {
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

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(PhysicalAddress $subject, User $user)
    {
        if ($subject->getLocked()) {
          return false;
        }
        return $user->getCustomerProfile() === $subject->getCustomerProfile();
    }

    private function canDelete(PhysicalAddress $subject, User $user)
    {
        if ($subject->getLocked()) {
          return false;
        }
        return $user->getCustomerProfile() === $subject->getCustomerProfile();
    }
}
