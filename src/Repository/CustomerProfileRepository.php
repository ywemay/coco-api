<?php

namespace App\Repository;

use App\Entity\CustomerProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CustomerProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerProfile[]    findAll()
 * @method CustomerProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerProfile::class);
    }

    // /**
    //  * @return CustomerProfile[] Returns an array of CustomerProfile objects
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
    public function findOneBySomeField($value): ?CustomerProfile
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
