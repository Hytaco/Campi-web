<?php

namespace App\Repository;

use App\Entity\Locaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Locaux|null find($id, $lockMode = null, $lockVersion = null)
 * @method Locaux|null findOneBy(array $criteria, array $orderBy = null)
 * @method Locaux[]    findAll()
 * @method Locaux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Locaux::class);
    }
    public function trisaldesc(){
        $em=$this->getEntityManager();
        $query=$em->createQuery('select s from App\Entity\Locaux s order by s.nom DESC');
        return $query->getResult();
    }
    public function trisalasc(){
        $em=$this->getEntityManager();
        $query=$em->createQuery('select s from App\Entity\Locaux s order by s.nom ASC');
        return $query->getResult();
    }
    // /**
    //  * @return Locaux[] Returns an array of Locaux objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Locaux
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
