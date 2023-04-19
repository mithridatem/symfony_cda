<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
class ApiArticleController extends AbstractController
{
    #[Route('/api/article/all', name:'app_api_article_all', methods:'GET')]
    public function getArticle(ArticleRepository $repo):Response{
        $articles = $repo->findAll();
        if(empty($articles)){
           // dd('test');
            return $this->json(['erreur'=>'Il n\'y a pas d\'article'], 206, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET']);
        }
        return $this->json($articles, 200, ['Content-Type'=>'application/json',
        'Access-Control-Allow-Origin'=> 'localhost',
        'Access-Control-Allow-Methods'=> 'GET'], ['groups'=>'article:readAll']);
    }

    #[Route('/api/article/id/{id}', name:'app_api_article_id', methods:'GET')]
    public function getArticleById(ArticleRepository $repo, int $id):Response{
        $article = $repo->find($id);
        if(empty($article)){
           // dd('test');
            return $this->json(['erreur'=>'l\'article n\'existe pas'], 206, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET']);
        }
        return $this->json($article, 200, ['Content-Type'=>'application/json',
        'Access-Control-Allow-Origin'=> 'localhost',
        'Access-Control-Allow-Methods'=> 'GET'], ['groups'=>'article:id']);
    }
}
