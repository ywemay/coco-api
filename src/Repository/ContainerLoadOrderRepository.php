<?php

namespace App\Repository;

use App\Entity\ContainerLoadOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ContainerLoadOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContainerLoadOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContainerLoadOrder[]    findAll()
 * @method ContainerLoadOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContainerLoadOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContainerLoadOrder::class);
    }

    // /**
    //  * @return ContainerLoadOrder[] Returns an array of ContainerLoadOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContainerLoadOrder
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
