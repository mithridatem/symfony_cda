<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\User;


class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(ArticleCrudController::class)
        ->generateUrl();
        return $this->redirect($url);
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Test Projet');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour à l\'accueil', 'fa fa-home', 'app_home');
        yield MenuItem::linkToCrud('Articles', 'fas fa-list', Article::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-newspaper', Categorie::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
    }
    public function __construct(private AdminUrlGenerator $adminUrlGenerator){}
}
