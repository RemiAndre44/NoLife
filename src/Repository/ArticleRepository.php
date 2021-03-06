<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

     /**
      * @return Article[] Returns an array of Article objects
      */
    public function findArticleByCategory($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.category = :val')
            ->setParameter('val', $id)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

 /**
      * @return Article[] Returns an array of Article objects
      */
    public function findArticleByCategoryQuery($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.category = :val')
            ->setParameter('val', $id)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
        ;
    }

    public function findArticlesQuery()
    {
        return $this->createQueryBuilder('a')
            ->orderBy("a.date", 'DESC')
            ->getQuery();
        ;
    }

    public function selectArticleById($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
    * @return Article[] Returns an array of Article objects
    */
    public function selectArticles()
    {
        return $this->createQueryBuilder('a')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult()
        ;
    }

    public function selectArticlesByField($field){

        return $this->createQueryBuilder('a')
            ->andWhere('a.content LIKE :val')
            ->orWhere('a.title LIKE :val')
            ->orWhere('a.sub_title LIKE :val')
            ->setParameter('val', '%'.$field.'%')
            ->getQuery()
            ->getResult()
        ;
    }
    
}
