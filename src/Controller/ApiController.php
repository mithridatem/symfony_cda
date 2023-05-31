<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Service\ApiRegister;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
    #[Route('/api/register', name:'app_api_register')]
    public function getToken(Request $request, UserRepository $repo,
        UserPasswordHasherInterface $hash, ApiRegister $apiRegister){
        //récupération du paramètre email
        $mail = $request->query->get('email');
        //récupération du paramètre password
        $password = $request->query->get('password');
        //test si le paramétre mail n'est pas saisi
        if(!$mail OR !$password){
            return $this->json(['Error'=>'informations absentes'], 400,['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*'] );
        }
        //test si le compte est authentifié
        if($apiRegister->authentification($hash,$repo,$mail,$password)){
            //récupération de la clé de chiffrement
            $secretKey = $this->getParameter('token');
            //génération du token
            $token = $apiRegister->genToken($mail, $secretKey, $repo);
            //Retourne le JWT
            return $this->json(['Token_JWT'=>$token], 200, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
        //test si le compte n'est pas authentifié (erreur mail ou password)
        else{
            return $this->json(['Error'=>'Informations de connexion incorrectes'], 400, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
    }
    #[Route('api/testToken', name:'app_api_testToken')]
    public function testToken(ApiRegister $apiRegister, Request $request){
        //récupération du token
        $jwt = substr($request->server->get('HTTP_AUTHORIZATION'),7);
        //récupération de la clé de chiffrement
        $secretKey = $this->getParameter('token');
        //récupération de la vérification du token
        $verif = $apiRegister->verifyToken($jwt, $secretKey);
        //dd($verif);
        //test sinon retourne l'erreur du token
        if($verif===true){
            return $this->json(['Accés authorisé'], 200, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
        //test si le token est valide
        else{
            return $this->json(['Error'=>$verif], 400, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
    }
}
