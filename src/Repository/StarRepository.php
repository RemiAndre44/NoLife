<?php

namespace App\Repository;

use App\Entity\Star;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Star|null find($id, $lockMode = null, $lockVersion = null)
 * @method Star|null findOneBy(array $criteria, array $orderBy = null)
 * @method Star[]    findAll()
 * @method Star[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Star::class);
    }


    /*
    public function findOneBySomeField($value): ?Star
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
