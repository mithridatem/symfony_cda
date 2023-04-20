<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
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
    #[Route('/api/article/add', name:'app_api_article_add', methods:'PUT')]
    public function addArticle(ArticleRepository $repo, Request $request,
    SerializerInterface $serialize, EntityManagerInterface $em, 
    CategorieRepository $repoCat, UserRepository $repoUser):Response{
        try{
            //récupérer le contenu de la requête
            $json = $request->getContent();
            //test si on à un json
            if(!$json){
                //renvoyer un json
                return $this->json(['erreur'=>'Le Json est vide ou n\'existe pas'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            //sérializer le json en tableau
            $data = $serialize->decode($json, 'json');

            //test si l'article existe déja
            $recup = $repo->findOneBy(['titre'=>$data['titre'], 'contenu'=>$data['contenu']]);
            //test doublon
            if($recup){
                //renvoyer un json
                return $this->json(['erreur'=>'L\'article '.$data['titre'].' existe déja en BDD'], 206, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            //instancier un objet article
            $article = new Article();
            //set des valeurs à l'objet
            $article->setTitre($data['titre']);
            $article->setContenu($data['contenu']);
            $article->setDate(new \DateTimeImmutable($data['date']));
            //test si le tableau user existe
            if(isset($data['user'])){
                //récupérer l'utilisateur en BDD
                $user = $repoUser->findOneBy(['email'=>$data['user']['email']]);
                //test si l'utilisateur n'existe pas
                if(!$user){
                    //renvoyer un json
                    return $this->json(['erreur'=>'L\'utilisateur '.$data['user']['email'].' n\'existe pas en BDD'], 401, 
                    ['Content-Type'=>'application/json',
                    'Access-Control-Allow-Origin'=> 'localhost',
                    'Access-Control-Allow-Methods'=> 'GET'],[]);
                }
                //sinon on le set
                $article->setUser($user);
            }    
            //test si le tableau catégorie existe
            if(isset($data['categories'])){
                //boucle pour vérifier si les catégories existent
                foreach ($data['categories'] as $value) {
                    //récupération de l'objet categorie
                    $cat = $repoCat->findOneBy(['nom'=>$value['nom']]);
                    //Test si la categorie n'existe pas
                    if(!$cat){
                        //renvoyer un json si elle n'existe pas
                        return $this->json(['erreur'=>'La categorie : '.$value['nom'].' existe pas en BDD'], 400, 
                        ['Content-Type'=>'application/json',
                        'Access-Control-Allow-Origin'=> 'localhost',
                        'Access-Control-Allow-Methods'=> 'GET'],[]);
                    }
                    //si elle existe
                    else{
                        //set de la categorie si elle existe
                        $article->addCategory($cat);
                    }
                }
            }
            //persister les données
            $em->persist($article);
            //enregistrer en BDD
            $em->flush();
            //renvoyer un json
            return $this->json(['erreur'=>'L\'article '.$article->getTitre().' a été ajouté en BDD'], 200, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
        //lever une exception
        catch(\Exception $e){
            //renvoyer un json
            return $this->json(['erreur : '=>$e->getMessage()], 400, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
    }
        
}
