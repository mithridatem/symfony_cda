<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\ApiRegister;
class ApiArticlesController extends AbstractController{
    #[Route('/api/articles/get/all', name:'app_api_articles_all')]
    public function getAllArticles(ArticleRepository $repo, ApiRegister $apiRegsiter, Request $request):Response{
        //vérifier le token 
        //récupérer la clé
        $secretKey = $this->getParameter('token');
        //récupérer le token 
        $jwt = substr($request->server->get('HTTP_AUTHORIZATION'),7);
        //test si le token n'existe pas
        if($jwt==''){
            return $this->json(['Error'=>'le token n\'existe pas'], 400, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
        $verif = $apiRegsiter->verifyToken($jwt, $secretKey);
        //si valide
        if($verif===true){
            //récupérer la liste des articles
            $data = $repo->findAll();
            //tester si on à bien des articles
            if($data){
                return $this->json($data, 200, ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> '*'], ['groups'=>'article:readAll']);
            }
            else{
                return $this->json(['Error'=>'Pas d\'articles en BDD'], 206, ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> '*']);
            }
        }
        //sinon on envoyer un json d'erreur (pas authorisé)
        else{
            return $this->json(['Error'=>$verif], 400, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }  
    }
}