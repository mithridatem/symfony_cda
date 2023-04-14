<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Form\ArticleType;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
        ]);
    }

    #[Route('/article/all', name:'app_article_all')]
    public function showAllArticle(ArticleRepository $articleRepository):Response{
        //récuperer dans un tableau tous les articles
        $articles = $articleRepository->findAll();
        return $this->render('article/index2.html.twig', [
            'liste'=> $articles,
        ]);
    }
    #[Route('/article/id/{id}', name:'app_article_id')]
    public function showArticleById(ArticleRepository $articleRepository, int $id):Response{
        //récupérer l'article depuis son id
        $article = $articleRepository->find($id);
        //retourner une interface twig avec l'article récupéré
        return $this->render('article/article.html.twig', [
            'article'=> $article,
        ]);
    }

    #[Route('/article/add', name:'app_article_add')]
    public function addArticle():Response{
        
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
    
        return $this->render('article/articleAdd.html.twig', [
            'form'=> $form->createView(),
        ]);
    }
}
