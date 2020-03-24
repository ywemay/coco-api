<?php

namespace App\Doctrine;

use App\Entity\ContainerLoadReport;
use Symfony\Component\Security\Core\Security;

class ContainerLoadReportListener
{
  private $security;

  public function __construct(Security $security)
  {
      $this->security = $security;
  }

  public function prePersist(ContainerLoadReport $data)
  {
    if (!$data->getCreatedAt()) {
      $date = new \DateTime();
      $data->setCreatedAt($date);
    }
    $price = $data->getSaleOrder()->getPrice();

    if (!$data->getAmountReceived()) {
      $data->setAmountReceived(0);
    }

    if (!$data->getAmountTip()) {
      $data->setAmountTip(0);
    }

    if (!$data->getTeamleaderTip()) {
      $data->setTeamleaderTip(5);
    }

    $workers = count($data->getWorkers());
    $total = $price + $data->getAmountTip();

    $data->setTotalAmount($total);
    $data->setCompanyProfit(ceil($data->getTotalAmount() * 0.15));

    $perWorker = $data->getTotalAmount() - $data->getCompanyProfit()
      - $data->getTeamleaderTip();

    if ($workers > 0) {
      $perWorker = $perWorker / $workers;
    }
    $data->setPerWorkerAmount($perWorker);

    $data->setBalance($data->getAmountReceived()-$price);
  }
}
