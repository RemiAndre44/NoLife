<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $catRepo)
    {

        $categories = $catRepo->selectCategories();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/article/{id}", name="article")
     */
    public function article(CategoryRepository $catRepo, ArticleRepository $aRepo, $id, Request $request)
    {

        $categories = $catRepo->selectCategories();
        $articles = $aRepo->findByArticleByCategory($id);

        return $this->render('article.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/blog/article/{id}", name="blog_show")
     */
    public function show(ArticleRepository $repo, $id, Request $request, ObjectManager $manager){
        //$repo = $this->getDoctrine()->getRepository(Article::class);
        
        $articles = $aRepo->findByArticleByCategory($id);
        return $this->render('article.html.twig',[
            'articles'=> $articles,
        ]);
    }

}
