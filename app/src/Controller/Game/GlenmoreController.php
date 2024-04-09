<?php

namespace App\Controller\Game;

use App\Entity\Game\Glenmore\GameGLM;
use App\Service\Game\Glenmore\GLMService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GlenmoreController extends AbstractController
{

    public function __construct(private GLMService $service)
    {}

    #[Route('/game/glenmore/{id}', name: 'app_game_show_glm')]
    public function showGame(GameGLM $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $isSpectator = false;
        $needToPlay = false;
        if ($player == null) {
            $player = $game->getPlayers()->get(0);
            $isSpectator = true;
        } else {
            //$needToPlay = $player->isTurnOfPlayer();
        }
        return $this->render('/Game/Glenmore/send.html.twig', [
            'game' => $game,
            'player' => $player,
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            //'isGameFinished' => $this->service->isGameEnded($game),
            'isGameFinished' => false,
            'selectedTile' => null,
            'adjacentTiles' => null,
            'potentialNeighbours' => null,
            'boardTiles' => $this->service->getTilesFromGame($game)
        ]);
    }
}