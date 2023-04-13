<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use App\Entity\Categorie;
use App\Entity\Article;
use App\Entity\User;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {   
        $tabCats=[];
        $userTab=[];
        $faker = Faker\Factory::create('fr_FR');
        for($i=0; $i<10; $i++){
            $cat = new Categorie();
            $cat->setNom($faker->jobTitle());
            $manager->persist($cat);
            $tabCats[]= $cat;
        }
        for($i=0; $i<5; $i++){
            $user = new User();
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            $user->setPassword(password_hash('1234', PASSWORD_DEFAULT));
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $manager->persist($user);
            $userTab[]= $user;
        }
        for($i=0; $i<10; $i++){
            $article = new Article();
            $article->setTitre($faker->words(3, true));
            $article->setContenu($faker->sentence(5));
            $article->setDate(new \DateTimeImmutable($faker->date('Y-m-d')));
            $article->setUser($userTab[$faker->numberBetween(0, 4)]);
            $article->addCategory($tabCats[$faker->numberBetween(0, 2)]);
            $article->addCategory($tabCats[$faker->numberBetween(3, 5)]);
            $article->addCategory($tabCats[$faker->numberBetween(6, 9)]);
            $manager->persist($article);
        }
        $manager->flush();
    }
}
