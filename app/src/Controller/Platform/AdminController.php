<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/dashboard/admin', name: 'app_dashboard_admin')]
    public function index(): Response
    {
        return $this->render('platform/admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
