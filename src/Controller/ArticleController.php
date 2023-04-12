<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
        ]);
    }

    #[Route('/article/all', name:'app_article_all')]
    public function showArticles():Response{
        //tableau indexe
        $article = ['Nouveau film', 'Contenu du nouveau film', 120];
        //tableau associatif
        $articles = [['titre'=> 'John Wick', 'contenu'=>'C\'est un film d\'action', 'duree'=> 140],
                    ['titre'=> 'autre', 'contenu'=>'C\'est un autre film', 'duree'=> 120]
        ];
        return $this->render('article/index2.html.twig', [
            'film'=> $article,
            'films' => $articles,
        ]);
    }
}
