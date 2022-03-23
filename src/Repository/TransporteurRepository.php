<?php

namespace App\Repository;

use App\Entity\Transporteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transporteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transporteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transporteur[]    findAll()
 * @method Transporteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransporteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transporteur::class);
    }

    public function tridesc(){
        $em=$this->getEntityManager();
        $query=$em->createQuery('select s from App\Entity\Transporteur s order by s.nom DESC');
        return $query->getResult();
    }
    public function triasc(){
        $em=$this->getEntityManager();
        $query=$em->createQuery('select s from App\Entity\Transporteur s order by s.nom ASC');
        return $query->getResult();
    }
    // /**
    //  * @return Transporteur[] Returns an array of Transporteur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transporteur
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
