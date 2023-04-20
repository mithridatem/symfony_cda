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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\Utils;
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
            //test si on n'à pas de json
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
            $article->setTitre(Utils::cleanInputStatic($data['titre']));
            $article->setContenu(Utils::cleanInputStatic($data['contenu']));
            $article->setDate(new \DateTimeImmutable(Utils::cleanInputStatic($data['date'])));
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
            return $this->json(['erreur'=>$e->getMessage()], 400, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
    }
    #[Route('/api/article/delete/{id}', name:'app_api_article_delete', methods:'DELETE')]
    public function delArticle(int $id, ArticleRepository $repoArt,
    EntityManagerInterface $em):Response{
        try{
            //tester si l'article existe
            $article = $repoArt->find($id);
            if(!$article){
                //renvoyer un json
                return $this->json(['erreur'=>'L\'article n\'existe pas en BDD'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            //supprimer
            $em->remove($article);
            $em->flush();
            //renvoyer un json
            return $this->json(['erreur'=>'L\'article '.$article->getTitre().' a été supprimé en BDD'], 200, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
        catch(\Exception $e){
            return $this->json(['erreur'=>$e->getMessage()], 500, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
    }
    #[Route('/api/article/delete', name:'app_api_article_json_delete', methods:'DELETE')]
    public function delArticleJson(ArticleRepository $repoArt, Request $request,
    EntityManagerInterface $em, SerializerInterface $serialize):Response{
        try{
            //récupérer le contenu de la requête
            $json = $request->getContent();
            //test si on n'à pas de json
            if(!$json){
                //renvoyer un json
                return $this->json(['erreur'=>'Le Json est vide ou n\'existe pas'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            //transformer sérialiser le json en tableau
            $data = $serialize->decode($json, 'json');
            //récupérer l'article
            $article = $repoArt->findOneBy(['titre'=>$data['titre']]);
            //tester si l'article existe
            if(!$article){
                //renvoyer un json
                return $this->json(['erreur'=>'L\'article n\'existe pas en BDD'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            //supprimer
            $em->remove($article);
            $em->flush();
            //renvoyer un json
            return $this->json(['erreur'=>'L\'article '.$article->getTitre().' a été supprimé en BDD'], 200, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
        catch(\Exception $e){
            return $this->json(['erreur'=>$e->getMessage()], 500, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);;
        }
    }
    #[Route('/api/article/update', name:'app_api_article_update', methods:'PATCH')]
    public function updateArticle(ArticleRepository $repoArt, Request $request,
    EntityManagerInterface $em, SerializerInterface $serialize):Response{
        try {
            //récupérer le json
            $json = $request->getContent();
            //test si on n'à pas de json
            if(!$json){
                //renvoyer un json
                return $this->json(['erreur'=>'Le Json est vide ou n\'existe pas'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            //transformer le json en tableau
            $data = $serialize->decode($json, 'json');
            //test si les champs sont vides
            if(empty($data['titre']) OR empty($data['contenu']) OR empty($data['date'])){
                //renvoyer un json
                return $this->json(['erreur'=>'Veuillez remplir les valeurs'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            //récupérer l'article
            $article = $repoArt->find($data['id']);
            //test si l'article
            if(!$article){
                //renvoyer un json
                return $this->json(['erreur'=>'L\'article : '.$data['titre'].' n\'existe pas'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            
            //test si la date est valide :
            if(!Utils::isValid($data['date'])){
                 //renvoyer un json
                return $this->json(['erreur'=>$data['date'].' n\'est pas une date valide'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            //mettre à jour l'objet
            $article->setTitre(Utils::cleanInputStatic($data['titre']));
            $article->setContenu(Utils::cleanInputStatic($data['contenu']));
            $article->setDate(new \DateTimeImmutable(Utils::cleanInputStatic($data['date'])));
            //persister et enregistrer les données
            $em->persist($article);
            $em->flush();
            //renvoyer un json
            return $this->json(['erreur'=>'L\'article : '.$article->getTitre().'a été mis à jour en BDD'], 200, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        } catch (\Exception $e) {
            return $this->json(['erreur'=>$e->getMessage()], 500, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
    }
}
