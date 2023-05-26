<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Messagerie;
class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }
    #[Route('/contact/form', name:'app_contact_form')]
    public function contactForm(EntityManagerInterface $em, Request $request,
    ContactRepository $repo, Messagerie $messagerie):Response{
        $msg = "";
        $statut = "";
        $contact = New Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        //test si le formulaire est submit 
        if($form->isSubmitted()AND $form->isValid()){
            //récupération du doublon
            $recup = $repo->findOneBy(['nom'=>$contact->getNom(), 
            'mail'=>$contact->getMail(), 
            'prenom'=>$contact->getPrenom(), 
            'objet'=>$contact->getObjet(), 
            'contenu'=>$contact->getContenu()]);
            //test si le message n'est pas un doublon
            if(!$recup){
                $contact->setContenu(Utils::cleanInputStatic($request->request->all('contact')['contenu']));
                $contact->setObjet(Utils::cleanInputStatic($request->request->all('contact')['objet']));
                $contact->setNom(Utils::cleanInputStatic($request->request->all('contact')['nom']));
                $contact->setPrenom(Utils::cleanInputStatic($request->request->all('contact')['prenom']));
                $contact->setMail(Utils::cleanInputStatic($request->request->all('contact')['mail']));
                $em->persist($contact);
                $em->flush();
                $msg = "Demande de contact ajoutée en BDD";
                //récupération des identifiants de messagerie
                $login = $this->getParameter('login');
                $mdp = $this->getParameter('mdp');
                //variable pour le mail
                $date = $contact->getDate()->format('d-m-Y');
                $objet = $contact->getObjet();
                $content = '<p>Nom : <strong>'.$contact->getNom().'</strong></p><hr>'.
                '<p>Prenom : <strong>'.$contact->getPrenom().'</strong></p><hr>'.
                '<p>Mail : <strong>'.$contact->getMail().'</strong></p><hr>'.
                '<p>Contenu : </p><strong>'.mb_convert_encoding($contact->getContenu(), 'ISO-8859-1', 'UTF-8').'</strong><hr>'.
                '<p>Date envoi : <strong>'.$date.'</strong>';
                $destinataire = "mathieumithridate@adrar-formation.com";
                //on stocke la fonction dans une variable
                $statut = $messagerie->sendEmail($login, $mdp, $objet, $content, $destinataire);
            }
            else{
                $msg = "La demande de contact existe déja en BDD";
            }
        }
        return $this->render('contact/index.html.twig',[
            'message' => $msg,
            'form'=>$form->createView(),
            'statut' => $statut
        ]);
    }
}
