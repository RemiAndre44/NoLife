<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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

    public function findArticlesQuery()
    {
        return $this->createQueryBuilder('a')
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
