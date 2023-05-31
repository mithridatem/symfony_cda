<?php
namespace App\Service;
use App\Repository\UserRepository;
use App\Service\Utils;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    class ApiRegister{
        //fonction pour tester l'authentification
        public function authentification(UserPasswordHasherInterface $hash,
        UserRepository $repo, $email, $password){
            //nettoyage du password
            $password = Utils::cleanInputStatic($password);
            //Récupération du compte
            $recup = $repo->findOneBy(['email'=>Utils::cleanInputStatic($email)]);
            //test si le compte existe
            if($recup){
                //test si le password est valide
                if(!$hash->isPasswordValid($recup, $password)){
                    return false;
                }
                //test sinon si le password est incorrect
                else{
                    return true;
                }
            }
            //test sinon le compte n'existe pas
            else{
                return false;
            }
            
        }
        //fonction pour générer le token JWT
        public function genToken($mail,$secretKey,$repo){
            //construction du JWT
            require_once('../vendor/autoload.php');
            //Variables pour le token
            $issuedAt   = new \DateTimeImmutable();
            $expire     = $issuedAt->modify('+1 minutes')->getTimestamp();
            $serverName = "your.domain.name";
            $username   = $repo->findOneBy(['email'=>$mail])->getNom();
            //Contenu du token
            $data = [
                'iat'  => $issuedAt->getTimestamp(),         // Timestamp génération du token
                'iss'  => $serverName,                       // Serveur
                'nbf'  => $issuedAt->getTimestamp(),         // Timestamp empécher date antérieure
                'exp'  => $expire,                           // Timestamp expiration du token
                'userName' => $username,                     // Nom utilisateur
            ];
            //retourne le JWT token encode
            $token = JWT::encode(
                $data,
                $secretKey,
                'HS512'
            );
            return $token;
        }
        //fonction pour véfifier si le token JWT est valide
        public function verifyToken($jwt, $secretKey){
            require_once('../vendor/autoload.php');
            try {
                //Décodage du token
                $token = JWT::decode($jwt, new Key($secretKey, 'HS512'));
                return true;
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
    }