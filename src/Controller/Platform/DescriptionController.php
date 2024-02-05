<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DescriptionController extends AbstractController
{
    #[Route('/description', name: 'app_desc')]
    public function index(): Response
    {
        return $this->render('platform/game/description.html.twig', [
            'controller_name' => 'DescriptionController',
        ]);
    }
}
