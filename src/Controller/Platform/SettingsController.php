<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    /**
     * Display the settings page
     * 
     * @return Response HTTP response: settings page
     */
    #[Route('/settings', name: 'app_settings')]
    public function index(): Response
    {
        return $this->render('platform/users/settings.html.twig', [
            'controller_name' => 'SettingsController',
        ]);
    }
}
