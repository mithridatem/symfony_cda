<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Utils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function userAdd(EntityManagerInterface $em, UserRepository $repo,
    Request $request, UserPasswordHasherInterface $hash):Response
    {   
        $msg = "";
        //Instancier un objet User
        $user = new User();
        //instancier un objet formulaire
        $form = $this->createForm(UserType::class, $user);
        //récupérer les données
        $form->handleRequest($request);
        //récupération d'un compte utilisateur
        $recup = $repo->findOneBy(['email'=>$user->getEmail()]);
        //test si le formulaire est submit
        if($form->isSubmitted() AND $form->isValid()){
            //tester si le compte existe
            if($recup){
                $msg = "Le compte : ".$user->getEmail()." existe déja";
            }
            else{
                //récupération du password
                $pass = Utils::cleanInputStatic($request->request->all('user')['password']['first']);
                //hashage du password
                $hash = $hash->hashPassword($user, $pass);
                //nettoyage des inputs
                $nom = Utils::cleanInputStatic($request->request->all('user')['nom']);
                $prenom = Utils::cleanInputStatic($request->request->all('user')['prenom']);
                $email = Utils::cleanInputStatic($request->request->all('user')['email']);
                //set des attributs nettoyé
                $user->setPassword($hash);
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setEmail($email);
                $user->setRoles(["ROLE_USER"]);
                //persister les données
                $em->persist($user);
                //ajoute en BDD
                $em->flush();
                $msg = "Le compte : ".$user->getEmail()." a été ajouté en BDD";
            }
        }
        return $this->render('register/index.html.twig', [
            'msg'=> $msg,
            'form'=> $form->createView(),
        ]);
    }
}
