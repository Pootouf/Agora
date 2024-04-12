<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Service\Game\GameManagerService;
use App\Service\Game\LogService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameNavigationMenuController extends AbstractController
{


    public function __construct(private readonly GameManagerService $gameService,
                                private readonly LogService $logService) {}

    #[Route('/game/{gameId}/exclude/player/{playerId}', name: 'app_game_exclude_player')]
    public function excludeAPlayer(
        int $gameId,
        int $playerId
    ): Response
    {
        $game = $this->gameService->getGameFromId($gameId);
        $player = $this->gameService->getPlayerFromId($playerId);
        $this->gameService->excludePlayer($player, $game);

        $this->logService->sendPlayerLog($game, $player,
            "Un joueur a exclu " . $player->getUsername() .
            " de la partie " . $game->getGameName() . " " . $game->getId() . " pour cause d'inactivité");
        return new Response("A player has been excluded, game is paused while no user takes its place",
            Response::HTTP_OK);
    }

    #[Route('/game/{gameId}/include/player', name: 'app_game_replace_player')]
    public function includeAPlayer(
        int $gameId
    ): Response
    {
        $username = $this->getUser()->getUsername();
        $game = $this->gameService->getGameFromId($gameId);
        $player = $this->gameService->getExcludedPlayerFromGameId($gameId);
        $this->gameService->replacePlayer($player, $username, $game);

        $this->logService->sendPlayerLog($game, $player,
            "Un joueur a exclu " . $player->getUsername() .
            " de la partie " . $game->getGameName() . " " . $game->getId() . " pour cause d'inactivité");
        return new Response("A new user joined the game",
            Response::HTTP_OK);
    }
}