<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\All;

class CategorieController extends AbstractController
{
    #[Route('/categorie/add', name: 'app_categorie_add')]
    public function addCategorie(EntityManagerInterface $em, Request $request,
        CategorieRepository $repo): Response
    {   
        $msg = "";
        //instancier un objet categorie
        $categorie = new Categorie();
        //créer le formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        //Récupération des datas du formulaire
        $form->handleRequest($request);
        //tester si le formulaire est submit
        if($form->isSubmitted() AND $form->isValid()){
            //récupération de l'enregistrement
            $recup = $repo->findOneBy(['nom'=>$categorie->getNom()]);
            //tester si la catégorie existe déja
            if(!$recup){
                //persister les données du formulaire
                $em->persist($categorie);
                //ajouter en BDD
                $em->flush();
                $msg = "L'article ".$categorie->getNom()." a été ajouté en BDD";
            }
            else{
                $msg = "L'article ".$categorie->getNom()." existe déja en BDD";
            }
        }
        return $this->render('categorie/categorieAdd.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
        ]);
    }

    #[Route('/categorie/all', name:'app_categorie_all')]
    public function showAllCategorie(CategorieRepository $repo):Response{
        $msg = "";
        //récupérer toutes les catégories
        $categories = $repo->findAll();
        //test si il n'y a aucunce catégorie
        if(!$categories){
            $msg = "Il n'y à pas de catégorie dans la BDD";
        }
        return $this->render('categorie/index.html.twig', [
            'msg'=> $msg,
            'categories'=> $categories
        ]);
    }
    #[Route('/categorie/update/{id}', name:'app_categorie_update')]
    public function updateCategorie(int $id, CategorieRepository $repo,
    EntityManagerInterface $em, Request $request,){
        $msg = "";
        //Récupérer la catégorie
        $categorie = $repo->find($id);
        //créer le formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        //Récupération des datas du formulaire
        $form->handleRequest($request);
        //tester si le formulaire est submit
        if($form->isSubmitted() AND $form->isValid()){
            //persister les données du formulaire
            $em->persist($categorie);
            //ajouter en BDD
            $em->flush();
            $msg = "La catégorie : ".$categorie->getNom()." a été modifié en BDD";
        }
        return $this->render('categorie/categorieUpdate.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
        ]);
    }
    #[Route('/categorie/delete/{id}', name:'app_categorie_delete')]
    public function deleteCategorie(int $id, CategorieRepository $repo,
    EntityManagerInterface $em){
        $categorie = $repo->find($id);
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('app_categorie_all');
    }
}
