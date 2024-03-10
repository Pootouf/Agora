<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/dashboard/user/settings', name: 'app_settings')]
    public function index(): Response
    {
        $user = $this->getUser();

        $username = $user->getUsername();
        $email = $user->getEmail();

        return $this->render('platform/users/settings.html.twig', [
            'controller_name' => 'SettingsController',
            'username' => $username,
            'email' => $email,
        ]);
    }
}
