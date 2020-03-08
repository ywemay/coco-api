<?php

namespace App\Repository;

use App\Entity\SaleOrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SaleOrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaleOrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaleOrderItem[]    findAll()
 * @method SaleOrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleOrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleOrderItem::class);
    }

    // /**
    //  * @return SaleOrderItem[] Returns an array of SaleOrderItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SaleOrderItem
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
