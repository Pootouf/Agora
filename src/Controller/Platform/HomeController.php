<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Displays the homepage
     * 
     * @return Response HTTP response: Homepage
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('platform/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
