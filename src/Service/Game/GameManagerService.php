<?php

namespace App\Service\Game;


use App\Entity\Game\DTO\Game;
use App\Entity\Game\GameUser;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\SixQP\SixQPGameManagerService;

class GameManagerService
{
    private GameSixQPRepository $gameSixQPRepository;
    private array $gameManagerServices;

    public function __construct(GameSixQPRepository $gameSixQPRepository,
                                SixQPGameManagerService $sixQPGameManagerService)
    {
        $this->gameSixQPRepository = $gameSixQPRepository;
        $this->gameManagerServices[AbstractGameManagerService::$SIXQP_LABEL] = $sixQPGameManagerService;
    }

    public function createGame(string $gameName): int {
        return $this->gameManagerServices[$gameName]->createGame();
    }

    public function joinGame(int $gameId, GameUser $user): int {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        return $this->gameManagerServices[$game->getGameName()]->createPlayer($user->getUsername(), $game);
    }

    public function quitGame(int $gameId, GameUser $user): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        if ($game->isLaunched()) {
            return -3;
        }
        return $this->gameManagerServices[$game->getGameName()]->deletePlayer($user->getUsername(), $game);
    }

    public function deleteGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        return $this->gameManagerServices[$game->getGameName()]->deleteGame($game);
    }

    public function launchGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        return $this->gameManagerServices[$game->getGameName()]->launchGame($game);
    }


    private function getGameFromId(int $gameId): ?Game {
        return $this->gameSixQPRepository->findOneBy(['id' => $gameId]);
    }

}