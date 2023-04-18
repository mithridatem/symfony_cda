<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
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
        $article = $articleRepository->find(Utils::cleanInputStatic($id));
        //retourner une interface twig avec l'article récupéré
        return $this->render('article/article.html.twig', [
            'article'=> $article,
        ]);
    }

    #[Route('/article/add', name:'app_article_add')]
    public function addArticle(ArticleRepository $repo,
        EntityManagerInterface $em, Request $request):Response{
        $msg = "";
        //Instance d'un objet article
        $article = new Article();
        //instance du formulaire
        $form = $this->createForm(ArticleType::class, $article);
        //Récupération des datas du formulaire
        $form->handleRequest($request);
        //Vérification du formulaire
        if($form->isSubmitted() AND $form->isValid()){
            //récupérer l'article
            $recup = $repo->findOneBy(['titre'=>$article->getTitre()]);
            //tester si il n'existe pas
            if(!$recup){
                 //on fait persister les données
                $em->persist($article);
                //on synchronise avec la BDD
                $em->flush();
                //gestion du message de confirmation
                $msg = 'L\'article : '.$article->getId().' à été ajouté'; 
            }
            else{
                $msg = 'L\'article : '.$article->getTitre().' Existe déja';
            }
        }
        //retourner l'interface twig
        return $this->render('article/articleAdd.html.twig', [
            'form'=> $form->createView(),
            'msg' => $msg,
        ]);
    }
    #[Route('/article/update/{id}', name:'app_article_update')]
    public function updateArticle(int $id, ArticleRepository $articleRepository, 
        EntityManagerInterface $em, Request $request):Response{
        $msg = "";
        //récupération de l'objet article
        $article = $articleRepository->find($id);
        //instance du formulaire
        $form = $this->createForm(ArticleType::class, $article);
        //Récupération des datas du formulaire
        $form->handleRequest($request);
        //Vérification du formulaire
        if($form->isSubmitted() AND $form->isValid()){
            //on fait persister les données
            $em->persist($article);
            //on synchronise avec la BDD
            $em->flush();
            //gestion du message de confirmation
            $msg = 'L\'article : '.$article->getId().' à été modifié'; 
        }
        //retourner l'interface twig
        return $this->render('article/articleUpdate.html.twig', [
            'form'=> $form->createView(),
            'msg'=> $msg,
        ]);
    }
}
