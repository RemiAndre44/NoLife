<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Quote;
use App\Entity\QuoteLike;
use App\Entity\CommentLike;
use App\Entity\Movie;
use App\Entity\Comment;
use App\Entity\PostLike;
use App\Repository\ArticleRepository;
use App\Repository\StarRepository;
use App\Repository\PostLikeRepository;
use App\Repository\QuoteLikeRepository;
use App\Repository\CommentLikeRepository;
use App\Repository\QuoteRepository;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;

class LikeController extends AbstractController
{
    /**
     * @Route("/quote/{id}/like", name="quote_like")
     */
    public function quoteLike(Quote $quote, ObjectManager $manager, QuoteLikeRepository $qlRepo)
    {

        $user = $this->getUser();

        if(!$user) return $this->json(['code' => 403, 'message' => 'Il faut être connecté'], 403);

        if($quote->isLikedByUser($user)){
            $like = $qlRepo->findOneBy([
                'quote' => $quote,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'Like supprimé',
                'likes' => $qlRepo->count(['quote' => $quote])
            ], 200);
        }

        $like = new QuoteLike();
        $like->setQuote($quote)
             ->setUser($user);
        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Like ajouté',
            'likes' => $qlRepo->count(['quote' => $quote])
         ], 200);

    }

    /**
     * @Route("/comment/{id}/like", name="comment_like")
     */
    public function commentLike(Comment $comment, ObjectManager $manager, CommentLikeRepository $clRepo)
    {

        $user = $this->getUser();

        if(!$user) return $this->json(['code' => 403, 'message' => 'Il faut être connecté'], 403);

        if($comment->isLikedByUser($user)){
            $like = $clRepo->findOneBy([
                'comment' => $comment,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like supprimé',
                'likes' => $clRepo->count(['comment' => $comment])
            ], 200);
        }

        $like = new CommentLike();
        $like->setComment($comment)
             ->setUser($user);
        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Like ajouté',
            'likes' => $clRepo->count(['comment' => $comment])
         ], 200);

    }


    /**
     * @Route("/article/{id}/like", name="article_like")
     */
    public function like(Article $article, ObjectManager $manager, PostLikeRepository $likeRepo)
    {
        $user = $this->getUser();

        if(!$user) return $this->json(['code' => 403, 'message' => 'Il faut être connecté'], 403);

        if($article->isLikedByUser($user)){
            $like = $likeRepo->findOneBy([
                'article' => $article,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'Like supprimé',
                'likes' => $likeRepo->count(['article' => $article])
             ], 200);
        }

        $like = new PostLike();
        $like->setArticle($article)
             ->setUser($user);
        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Like ajouté',
            'likes' => $likeRepo->count(['article' => $article])
         ], 200);
    }

    /**
     * @Route("/movie/{id}/stars/{rate}", name="movie_star")
     */
    public function movieStars(Movie $movie, ObjectManager $manager, StarRepository $starRepo, $rate)
    {

        $user = $this->getUser();

        if(!$user) return $this->json(['code' => 403, 'message' => 'Il faut être connecté'], 403);

        if($movie->hasAVoteFromUser($user)){
            $star = $starRepo->findOneBy([
                'movie' => $movie,
                'user' => $user
            ]);

            $star->setRate($rate);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Note modifiée',
                'stars' => $rate], 200);

        }

        $star = new Star();
        $star->setMovie($movie)
             ->setUser($user)
             ->setRate($rate);
        $manager->persist($star);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Vote ajouté',
            'stars' => $rate], 200);
    }
}
