<?php
// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use App\Entity\SaleOrder;
use App\Entity\ContainerLoadOrder;
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
    return;
  }

  private function addUserRelatedWhere(QueryBuilder $queryBuilder): void
  {
    $rootAlias = $queryBuilder->getRootAliases()[0];
    if ($this->security->isGranted('ROLE_TEAMLEADER')) {
      $queryBuilder->andWhere(sprintf('%s.roles LIKE :role1', $rootAlias));
      $queryBuilder->andWhere(sprintf('%s.roles LIKE :role2', $rootAlias));
      $queryBuilder->setParameter('role1', '%"ROLE_WORKER"%');
      $queryBuilder->setParameter('role2', '%"ROLE_WORKER"%');
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
      $queryBuilder->join("App\Entity\Company", 'c', 'WITH', $rootAlias.'.company = c.id');
      $queryBuilder->andWhere('c.owner = :uid');
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
}
