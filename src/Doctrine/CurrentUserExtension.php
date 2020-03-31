<?php
// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use App\Entity\SaleOrder;
use App\Entity\CustomerProfile;
use App\Entity\ContainerLoadOrder;
use App\Entity\PhysicalAddress;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
  {
    $this->addWhere($queryBuilder, $resourceClass);
  }

  public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
  {
    $this->addWhere($queryBuilder, $resourceClass);
  }

  private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
  {
    if ($this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
      return;
    }
    if (User::class == $resourceClass) {
      $this->addUserRelatedWhere($queryBuilder);
    }
    elseif (SaleOrder::class == $resourceClass) {
      $this->addSaleOrderRelatedWhere($queryBuilder);
    }
    elseif (ContainerLoadOrder::class == $resourceClass) {
      $this->addContainerLoadOrderRelatedWhere($queryBuilder);
    }
    elseif (CustomerProfile::class == $resourceClass) {
      $this->addCustomerProfileConditions($queryBuilder);
    }
    elseif (PhysicalAddress::class == $resourceClass) {
      $this->addPhysicalAddressConditions($queryBuilder);
    }
    return;
  }

  private function addUserRelatedWhere(QueryBuilder $queryBuilder): void
  {
    $rootAlias = $queryBuilder->getRootAliases()[0];
    if ($this->security->isGranted('ROLE_TEAMLEADER')) {
      $queryBuilder->andWhere(sprintf('%s.roles LIKE :role1 OR %s.roles LIKE :role2', $rootAlias, $rootAlias));
      // $queryBuilder->andWhere(sprintf('%s.roles LIKE :role2', $rootAlias));
      $queryBuilder->setParameter('role1', '%"ROLE_WORKER"%');
      $queryBuilder->setParameter('role2', '%"ROLE_TEAMLEADER"%');
    }
    else {
      $user = $this->security->getUser();
      $queryBuilder->andWhere(sprintf('%s.id = :id', $rootAlias));
      $queryBuilder->setParameter('id', $user->getId());
    }
  }

  private function addSaleOrderRelatedWhere(QueryBuilder $queryBuilder): void
  {
    $rootAlias = $queryBuilder->getRootAliases()[0];
    if ($this->security->isGranted('ROLE_CUSTOMER')) {
      $user = $this->security->getUser();
      $queryBuilder->andWhere($rootAlias . '.owner = :uid');
      $queryBuilder->setParameter('uid', $user->getId());
    }
  }

  private function addContainerLoadOrderRelatedWhere(QueryBuilder $queryBuilder): void
  {
    $rootAlias = $queryBuilder->getRootAliases()[0];
    if ($this->security->isGranted('ROLE_TEAMLEADER')) {
      $user = $this->security->getUser();
      $queryBuilder->andWhere($rootAlias . '.assignedTo = :uid');
      $queryBuilder->setParameter('uid', $user->getId());
    }
  }

  private function addCustomerProfileConditions(QueryBuilder $queryBuilder): void
  {
    $rootAlias = $queryBuilder->getRootAliases()[0];
    if ($this->security->isGranted('ROLE_CUSTOMER')) {
      $user = $this->security->getUser();
      $queryBuilder->andWhere($rootAlias . '.customerProfile = :pid');
      $queryBuilder->setParameter('pid', $user->getCustomerProfile()->getId());
    }
  }

  private function addPhysicalAddressConditions(QueryBuilder $queryBuilder): void
  {
    $rootAlias = $queryBuilder->getRootAliases()[0];
    if ($this->security->isGranted('ROLE_CUSTOMER')) {
      $user = $this->security->getUser();
      $queryBuilder->join($rootAlias . '.customerProfile', 'p');
      $queryBuilder->andWhere($rootAlias . '.customerProfile = :pid');
      $queryBuilder->setParameter('pid', $user->getCustomerProfile()->getId());
    }
  }
}
