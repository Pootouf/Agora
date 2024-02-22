<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\GameUser;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Service\Game\SixQP\SixQPGameManagerService;
use App\Service\Game\Splendor\SPLGameManagerService;

class GameManagerService
{
    private GameSixQPRepository $gameSixQPRepository;
    private GameSPLRepository $gameSPLRepository;
    private array $gameManagerServices;

    public function __construct(GameSixQPRepository $gameSixQPRepository,
                                GameSPLRepository $gameSPLRepository,
                                SixQPGameManagerService $sixQPGameManagerService,
                                SPLGameManagerService $SPLGameManagerService)
    {
        $this->gameSixQPRepository = $gameSixQPRepository;
        $this->gameManagerServices[AbstractGameManagerService::$SIXQP_LABEL] = $sixQPGameManagerService;
        $this->gameManagerServices[AbstractGameManagerService::$SPL_LABEL] = $SPLGameManagerService;
        $this->gameSPLRepository = $gameSPLRepository;
    }

    public function createGame(string $gameName): int {
        return $this->gameManagerServices[$gameName]->createGame();
    }

    public function joinGame(int $gameId, GameUser $user): int {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        return $this->gameManagerServices[$game->getGameName()]->createPlayer($user->getUsername(), $game);
    }

    public function quitGame(int $gameId, GameUser $user): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        if ($game->isLaunched()) {
            return AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        return $this->gameManagerServices[$game->getGameName()]->deletePlayer($user->getUsername(), $game);
    }

    public function deleteGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        return $this->gameManagerServices[$game->getGameName()]->deleteGame($game);
    }

    public function launchGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        return $this->gameManagerServices[$game->getGameName()]->launchGame($game);
    }


    private function getGameFromId(int $gameId): ?Game {
        $game = $this->gameSixQPRepository->findOneBy(['id' => $gameId]);
        if ($game == null) {
            $game = $this->gameSPLRepository->findOneBy(['id' => $gameId]);
        }
        return $game;
    }

}