<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Article;
use App\Entity\Quote;
use App\Entity\QuoteLike;
use App\Entity\CommentLike;
use App\Entity\Comment;
use App\Entity\PostLike;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\PostLikeRepository;
use App\Repository\QuoteLikeRepository;
use App\Repository\CommentLikeRepository;
use App\Repository\LinksRepository;
use App\Form\CommentFormType;
use App\Form\QuoteFormType;
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
    public function index(CategoryRepository $catRepo, ArticleRepository $aRepo, QuoteRepository $qRepo, PaginatorInterface $paginator, Request $request)
    {

        $categories = $catRepo->selectCategories();
        $articlesQuery = $aRepo->findArticlesQuery();

        $articles = $paginator->paginate(
            $articlesQuery, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

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
    public function article(Article $article,CategoryRepository $catRepo, ArticleRepository $aRepo, CommentRepository $comRepo, $id, Request $request, ObjectManager $manager, LinksRepository $lRepo)
    {
        $categories = $catRepo->selectCategories();
        $comments = $comRepo->findCommentsByArticle($id);
        $article = $aRepo->selectArticleById($id);
        $links = $lRepo->findByArticle($id);

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

            return $this->redirectToRoute('article',array('id' => $article->getId()));
        }


        return $this->render('article.html.twig', [
            'article' => $article,
            'categories' => $categories,
            'comments' => $comments,
            'commentForm' => $form->createView(),
            'links' => $links
        ]);
    }


    /**
     * @Route("/category/{id}", name="category")
     */
    public function category($id, CategoryRepository $catRepo, ArticleRepository $aRepo, Request $request, ObjectManager $manager)
    {
        $categories = $catRepo->selectCategories();
        $articles = $aRepo->findArticleByCategory($id);

        return $this->render('articleBySearch.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/search", name="search")
     * @return [type] [description]
     */
    public function search(ArticleRepository $aRepo, Request $request, CategoryRepository $catRepo)
    {

        $categories = $catRepo->selectCategories();

        $articles = $aRepo->selectArticlesByField($request->get('query'));

        return $this->render('articleBySearch.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/quotes", name="quotes")
     */
    public function quotes(CategoryRepository $catRepo, QuoteRepository $qRepo, Request $request, ObjectManager $manager)
    {
        $categories = $catRepo->selectCategories();
        $quotes = $qRepo->findAll();

        $quote = new Quote();

        $form = $this->createForm(QuoteFormType::class, $quote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $quote->setUser($user);

            $manager->persist($quote);

            $manager->flush();

            return $this->redirectToRoute('quotes');
        }

        return $this->render('/quotes.html.twig',[
            'categories' => $categories,
            'quotes' => $quotes,
            'quoteForm' => $form->createView()
        ]);
    }

}
