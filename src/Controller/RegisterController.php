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
use App\Service\Messagerie;
class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function userAdd(EntityManagerInterface $em, UserRepository $repo,
    Request $request, UserPasswordHasherInterface $hash, Messagerie $messagerie):Response
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
            //test sinon le compte n'existe pas
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
                //setter activation à false
                $user->setActivate(false);
                //persister les données
                $em->persist($user);
                //ajoute en BDD
                $em->flush(); 
                //récupération des identifiants de messagerie
                $login = $this->getParameter('login');
                $mdp = $this->getParameter('mdp');
                //variable pour le mail 
                $objet = 'Activation de votre compte';
                $content = '<p>Pour activer votre compte veuillez cliquer sur l\'url ci-dessous</p>
                <a href="https://localhost:8000/register/activate/'.$user->getId().'>Activation</a>';
                $msg = "Le compte : ".$user->getEmail()." a été ajouté en BDD";
                //on stocke la fonction dans une variable
                $statut = $messagerie->sendEmail($login, $mdp, $objet, $content, $email);
            }
        }
        return $this->render('register/index.html.twig', [
            'msg'=> $msg,
            'form'=> $form->createView(),
        ]);
    }
    #[Route('/register/activate/{id}', name: 'app_register_activate')]
    public function activateUser($id, EntityManagerInterface $em, UserRepository $repo):Response{
        //récupérer le compte utilisateur
        $user = $repo->find($id);
        //tester si le compte existe
        if($user){
            $user->setActivate(true);
            $em->persist($user);
            $em->flush();
            //redirection vers la connexion
            return $this->redirectToRoute('app_login');
        }
        //test sinon le compte n'existe pas
        else{
            //redirection vers l'inscription
            return $this->redirectToRoute('app_register');
        }
    }

    //fonction qui envoi le mail d'activation
    #[Route('/sendMail/activate/{id}', name:'app_send_activate')]
    public function sendMailActivate(Utils $utils, 
    Messagerie $messagerie, UserRepository $repo,$id):Response{
        //nettoyage de l'id
        $id = $utils->cleanInput($id);
        //récupération des identifiant de messagerie
        $login = $this->getParameter('login');
        $mdp = $this->getParameter('mdp');
        //variable qui récupére l'utilisateur
        $user = $repo->find($id);
        if($user){
            $objet = 'activation du compte';
            $content = '<p>Pour activer votre compte veuillez cliquer ci-dessous
            </p><a href="localhost:8000/activate/'.$id.'">Activer</a>';
            //on stocke la fonction dans une variable
            $status = $messagerie->sendEmail($login, $mdp, $objet, $content, $user->getEmail());
            return new Response($status, 200, []);
        }
        else{
            return new Response('Le compte n\'existe pas', 200, []);
        }
    }
}
