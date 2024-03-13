<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('platform/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
    #[Route('/dashboard/profile', name: 'app_dashboard_profile')]
    public function profile(): Response
    {
        return $this->render('platform/dashboard/profile.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
    #[Route('/dashboard/settings', name: 'app_dashboard_settings')]
    public function settings(): Response
    {
        return $this->render('platform/dashboard/settings.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
