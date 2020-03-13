<?php
namespace App\Security;

use App\Entity\SaleOrderItem;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SaleOrderItemVoter extends Voter
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

        // only vote on SaleOrderItem objects inside this voter
        if (!$subject instanceof SaleOrderItem) {
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

        // you know $subject is a SaleOrderItem object, thanks to supports
        /** @var SaleOrderItem $saleOrderItem */
        $saleOrderItem = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($saleOrderItem, $user);
            case self::EDIT:
                return $this->canEdit($saleOrderItem, $user);
            case self::DELETE:
                return $this->canDelete($saleOrderItem, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(SaleOrderItem $saleOrderItem, User $user)
    {
        if ($this->canEdit($saleOrderItem, $user)) {
            return true;
        }
        return $user === $saleOrderItem->getOwner();
    }

    private function canEdit(SaleOrderItem $saleOrderItem, User $user)
    {
        if ($saleOrderItem->getState() > 0) {
          return false;
        }
        return $user === $saleOrderItem->getOwner();
    }

    private function canDelete(SaleOrderItem $saleOrderItem, User $user)
    {
        if ($saleOrderItem->getState() > 0) {
          return false;
        }
        return $user === $saleOrderItem->getOwner();
    }
}
