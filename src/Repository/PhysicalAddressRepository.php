<?php

namespace App\Repository;

use App\Entity\PhysicalAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PhysicalAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhysicalAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhysicalAddress[]    findAll()
 * @method PhysicalAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhysicalAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhysicalAddress::class);
    }

    // /**
    //  * @return PhysicalAddress[] Returns an array of PhysicalAddress objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhysicalAddress
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
