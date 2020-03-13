<?php
namespace App\Security;

use App\Entity\SaleOrder;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SaleOrderVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on SaleOrder objects inside this voter
        if (!$subject instanceof SaleOrder) {
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

        // you know $subject is a SaleOrder object, thanks to supports
        /** @var SaleOrder $saleOrder */
        $saleOrder = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($saleOrder, $user);
            case self::EDIT:
                return $this->canEdit($saleOrder, $user);
            case self::DELETE:
                return $this->canDelete($saleOrder, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(SaleOrder $saleOrder, User $user)
    {
        if ($this->canEdit($saleOrder, $user)) {
            return true;
        }
        return $user === $saleOrder->getOwner();
    }

    private function canEdit(SaleOrder $saleOrder, User $user)
    {
        if ($saleOrder->getState() > 0) {
          return false;
        }
        return $user === $saleOrder->getOwner();
    }

    private function canDelete(SaleOrder $saleOrder, User $user)
    {
        if ($saleOrder->getState() > 0) {
          return false;
        }
        return $user === $saleOrder->getOwner();
    }
}
