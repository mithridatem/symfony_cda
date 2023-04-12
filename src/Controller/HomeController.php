<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
     #[Route('/{nbr1}/{nbr2}', name: 'app_home_param')]
    public function index2($nbr1, $nbr2): Response
    {
        return $this->render('home/index2.html.twig', [
            'nbr1'=> $nbr1,
            'nbr2'=> $nbr2,
        ]);
    }
 
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);
    }
}
