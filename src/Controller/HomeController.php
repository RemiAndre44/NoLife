<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Article;
use App\Entity\Quote;
use App\Entity\QuoteLike;
use App\Entity\Comment;
use App\Entity\PostLike;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\PostLikeRepository;
use App\Repository\QuoteLikeRepository;
use App\Form\CommentFormType;
use App\Repository\CategoryRepository;
use App\Repository\QuoteRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $catRepo, ArticleRepository $aRepo, QuoteRepository $qRepo)
    {

        $categories = $catRepo->selectCategories();
        $articles = $aRepo->selectArticles();
        $quotes = $qRepo->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
            'quotes' => $quotes
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/article/{id}", name="article")
     */
    public function article(Article $article,CategoryRepository $catRepo, ArticleRepository $aRepo, CommentRepository $comRepo, $id, Request $request, ObjectManager $manager)
    {
        $categories = $catRepo->selectCategories();
        $comments = $comRepo->findAll();
        $article = $aRepo->selectArticleById($id);

        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setDate(new \DateTime());
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setArticle($article);

            $manager->persist($comment);

            $manager->flush();

            return $this->redirectToRoute('article');
        }


        return $this->render('article.html.twig', [
            'article' => $article,
            'categories' => $categories,
            'comments' => $comments,
            'commentForm' => $form->createView()
        ]);
    }

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
     * @Route("/category/{id}", name="category")
     */
    public function category($id, CategoryRepository $catRepo, ArticleRepository $aRepo, Request $request, ObjectManager $manager)
    {
        $categories = $catRepo->selectCategories();
        $articles = $aRepo->findByArticleByCategory($id);

        return $this->render('articleByCategory.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/quotes", name="quotes")
     */
    public function quotes(CategoryRepository $catRepo, QuoteRepository $qRepo)
    {
        $categories = $catRepo->selectCategories();
        $quotes = $qRepo->findAll();


        return $this->render('/quotes.html.twig',[
            'categories' => $categories,
            'quotes' => $quotes
        ]);
    }

}
