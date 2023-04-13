<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
        ]);
    }

    #[Route('/article/all', name:'app_article_all')]
    public function showArticles(ArticleRepository $articleRepository):Response{
        //récuperer dans un tableau tous les articles
        $articles = $articleRepository->findAll();
        return $this->render('article/index2.html.twig', [
            'liste'=> $articles,
        ]);
    }
}
