<?php

namespace App\Repository;

use App\Entity\Alerts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Alerts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alerts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alerts[]    findAll()
 * @method Alerts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alerts::class);
    }

    // /**
    //  * @return Alerts[] Returns an array of Alerts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Alerts
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
