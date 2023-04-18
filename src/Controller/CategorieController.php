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
        dd($request);
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
}
