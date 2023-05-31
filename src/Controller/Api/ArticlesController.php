<?php
namespace App\Controller\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
class ArticlesController extends AbstractController{
    #[Route('/articles/all', name:'app_articles_all')]
    public function getAllArticles():Response{
        return $this->render('api/index.html.twig');
    }
}
