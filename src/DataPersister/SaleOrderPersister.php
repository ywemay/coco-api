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
    $this->entityManager->persist($data);
    $this->entityManager->flush();
  }

  public function remove($data)
  {
    $this->entityManager->remove($data);
    $this->entityManager->flush();
  }
}
