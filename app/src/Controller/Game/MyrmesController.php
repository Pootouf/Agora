<?php

namespace App\Controller\Game;

use App\Entity\Game\Myrmes\GameMYR;
use App\Service\Game\Myrmes\MYRService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyrmesController extends AbstractController
{

    public function __construct(private readonly MYRService $service)
    {

    }

    #[Route('/game/myrmes/{id}', name: 'app_game_show_myr')]
    public function showGame(GameMYR $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());

        return $this->render('/Game/Myrmes/index.html.twig', [
            'player' => $player,
            'game' => $game,
            'tiles' => $game->getMainBoardMYR()->getTiles(),
            'preys' => $game->getMainBoardMYR()->getPreys()
        ]);
    }
}