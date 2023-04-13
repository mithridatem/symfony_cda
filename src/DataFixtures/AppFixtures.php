<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use App\Entity\Categorie;
use App\Entity\Article;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {   
        $tabCats=[];
        $faker = Faker\Factory::create('fr_FR');
        for($i=0; $i<10; $i++){
            $cat = new Categorie();
            $cat->setNom($faker->jobTitle());
            $manager->persist($cat);
            $tabCats[]= $cat;
        }
        for($i=0; $i<10; $i++){
            $article = new Article();
            $article->setTitre($faker->words(3, true));
            $article->setContenu($faker->sentence(5));
            $article->setDate(new \DateTimeImmutable($faker->date('Y-m-d')));
            $manager->persist($article);
        }
        $manager->flush();
    }
}
