<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Company;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class CompanyPersister implements DataPersisterInterface
{
  private $entityManager;
  private $security;

  public function __construct(EntityManagerInterface $entityManager, Security $security)
  {
    $this->entityManager = $entityManager;
    $this->security = $security;
  }

  public function supports($data): bool
  {
    return $data instanceof Company;
  }

  /**
   * @param  Company $data
   */
  public function persist($data)
  {

    if (!$this->security->isGranted('ROLE_ADMIN')) {
      print "Setting user: " . $this->security->getUser()->getUsername();
      $data->setOwner($this->security->getUser());
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
