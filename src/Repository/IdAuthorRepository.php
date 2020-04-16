<?php

namespace App\Repository;

use App\Entity\IdAuthor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IdAuthor|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdAuthor|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdAuthor[]    findAll()
 * @method IdAuthor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdAuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdAuthor::class);
    }

    // /**
    //  * @return IdAuthor[] Returns an array of IdAuthor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IdAuthor
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
