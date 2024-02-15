<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Game;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'app_game')]
    public function index(): Response
    {
        return $this->render('platform/game/game.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }
}
