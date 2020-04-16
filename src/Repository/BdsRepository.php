<?php

namespace App\Repository;

use App\Entity\Bds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Bds|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bds|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bds[]    findAll()
 * @method Bds[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BdsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bds::class);
    }

     /**
      * @return Bd[] Returns an array of Bd objects
      */
    public function findLastBds()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('m.date', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBdsQuery()
    {
        return $this->createQueryBuilder('b')
            ->getQuery();
        ;
    }

    // /**
    //  * @return Bds[] Returns an array of Bds objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bds
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
