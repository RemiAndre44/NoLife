<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Bds;
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
use App\Repository\StarRepository;
use App\Repository\BdsRepository;
use App\Repository\MovieRepository;
use App\Repository\LinksRepository;
use App\Form\CommentFormType;
use App\Form\QuoteFormType;
use App\Repository\CategoryRepository;
use App\Repository\QuoteRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $catRepo, ArticleRepository $aRepo, QuoteRepository $qRepo, PaginatorInterface $paginator, Request $request,MovieRepository $movieRepo, StarRepository $starRepo)
    {

        $categories = $catRepo->selectCategories();
        $articlesQuery = $aRepo->findArticlesQuery();
        $lastMovies = $movieRepo->findLastMovies();
        $starsList = $starRepo->findAll();

        $articles = $paginator->paginate(
            $articlesQuery, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        $quotes = $qRepo->findLastQuotes();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
            'quotes' => $quotes,
            'lastMovies' => $lastMovies,
            'starList' => $starsList
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/article/{id}", name="article")
     */
    public function article(Article $article,CategoryRepository $catRepo, ArticleRepository $aRepo, CommentRepository $comRepo, $id, Request $request, EntityManagerInterface $manager, LinksRepository $lRepo)
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
    public function category($id, CategoryRepository $catRepo, ArticleRepository $aRepo,PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager)
    {
        $categories = $catRepo->selectCategories();
        $articleQuery = $aRepo->findArticleByCategoryQuery($id);

        $articles = $paginator->paginate(
            $articleQuery, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

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
    public function quotes(CategoryRepository $catRepo, QuoteRepository $qRepo, Request $request, EntityManagerInterface $manager)
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

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/movies", name="movies")
     */
    public function movies(CategoryRepository $catRepo, MovieRepository $mRepo, PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager)
    {
        $categories = $catRepo->selectCategories();
        $moviesForStars = $mRepo->findAll();
        $moviesQuery = $mRepo->findMoviesQuery();

        $movies = $paginator->paginate(
            $moviesQuery,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('/movies.html.twig',[
            'categories' => $categories,
            'movies' => $movies,
            'moviesForStars' => $moviesForStars
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/bds", name="bds")
     */
    public function bds(CategoryRepository $catRepo, BdsRepository $bRepo, PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager)
    {
        $categories = $catRepo->selectCategories();
        $bdsForStars = $bRepo->findAll();
        $bdsQuery = $bRepo->findbdsQuery();

        $bds = $paginator->paginate(
            $bdsQuery,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('/bds.html.twig',[
            'categories' => $categories,
            'bds' => $bds,
            'bdsForStars' => $bdsForStars
        ]);
    }

}
