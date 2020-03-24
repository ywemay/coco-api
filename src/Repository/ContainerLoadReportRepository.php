<?php

namespace App\Repository;

use App\Entity\ContainerLoadReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ContainerLoadReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContainerLoadReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContainerLoadReport[]    findAll()
 * @method ContainerLoadReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContainerLoadReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContainerLoadReport::class);
    }

    // /**
    //  * @return ContainerLoadReport[] Returns an array of ContainerLoadReport objects
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
    public function findOneBySomeField($value): ?ContainerLoadReport
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
