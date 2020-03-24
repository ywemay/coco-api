<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\SaleOrder;
use App\Entity\ContainerLoadReport;
use Doctrine\ORM\EntityManagerInterface;

class ContainerLoadReportPersister implements DataPersisterInterface
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function supports($data): bool
  {
    return $data instanceof ContainerLoadReport;
  }

  /**
   * @param  ContainerLoadReport $data
   */
  public function persist($data)
  {
    dd($data);
    if (!$data->getCreatedAt()) {
      $data->setCreatedAt(date("Y-m-d H:i:s"));
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

    $this->entityManager->persist($data);
    $this->entityManager->flush();
  }

  public function remove($data)
  {
    $this->entityManager->remove($data);
    $this->entityManager->flush();
  }
}
