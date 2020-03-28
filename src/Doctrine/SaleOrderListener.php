<?php

namespace App\Doctrine;

use App\Entity\SaleOrder;
use Symfony\Component\Security\Core\Security;

class SaleOrderListener
{
  private $security;

  public function __construct(Security $security)
  {
      $this->security = $security;
  }

  public function prePersist(SaleOrder $data)
  {
    if (!$data->getDate()) {
      $data->setDate(date('Y-m-d'));
    }
    if (!$data->getOwner()) {
      $data->setOwner($this->security->getUser());
    }

    if (!$data->getCustomer()) {
      $user = $this->security->getUser();
      if ($user) {
        $data->setCustomer($user->getCustomerProfile());
      }
    }

    if (!$data->getAssignedTo() && $data->getState() == 1) {
      $data->setState(SaleOrder::FRESH);
    }
    elseif ($data->getAssignedTo() && $data->getState() == 0) {
      $data->setState(SaleOrder::PLANNED);
    }
  }
}
