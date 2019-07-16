<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Article;
use App\Entity\PostLike;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\PostLikeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $catRepo, ArticleRepository $aRepo)
    {

        $categories = $catRepo->selectCategories();
        $articles = $aRepo->selectArticles();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'articles' => $articles
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/article/{id}", name="article")
     */
    public function article(CategoryRepository $catRepo, ArticleRepository $aRepo, $id, Request $request)
    {

        $categories = $catRepo->selectCategories();

        $article = $aRepo->selectArticleById($id);

        return $this->render('article.html.twig', [
            'article' => $article,
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

    /**
     * @Route("/article/{id}/like", name="article_like")
     */
    public function like(Article $article, ObjectManager $manager, PostLikeRepository $likeRepo): Response
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
                'likes' => $likeRepo->count(['article'])
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

}
