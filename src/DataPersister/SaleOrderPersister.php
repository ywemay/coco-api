<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\SaleOrder;
use Doctrine\ORM\EntityManagerInterface;

class SaleOrderPersister implements DataPersisterInterface
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function supports($data): bool
  {
    return $data instanceof SaleOrder;
  }

  /**
   * @param  SaleOrder $data
   */
  public function persist($data)
  {
    if (!$data->getAssignedTo() && $data->getState() == 1) {
      $data->setState(SaleOrder::FRESH);
    }
    elseif ($data->getAssignedTo() && $data->getState() == 0) {
      $data->setState(SaleOrder::PLANNED);
    }
    $this->entityManager->persist($data);
    $this->entityManager->flush();
  }

  public function remove($data)
  {
    $this->entityManager->remove($data);
    $this->entityManager->flush();
  }
}
