<?php

namespace App\Repository;

use App\Entity\QuoteLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuoteLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuoteLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuoteLike[]    findAll()
 * @method QuoteLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuoteLike::class);
    }

    // /**
    //  * @return QuoteLike[] Returns an array of QuoteLike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuoteLike
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
