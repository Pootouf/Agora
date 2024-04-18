<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Platform\User;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Service\Game\Glenmore\GLMGameManagerService;
use App\Service\Game\Myrmes\MYRGameManagerService;
use App\Service\Game\SixQP\SixQPGameManagerService;
use App\Service\Game\Splendor\SPLGameManagerService;

class GameManagerService
{
    private array $gameManagerServices;

    public function __construct(private GameSixQPRepository $gameSixQPRepository,
                                private GameSPLRepository $gameSPLRepository,
                                private GameGLMRepository $gameGLMRepository,
                                private GameMYRRepository $gameMYRRepository,
                                SixQPGameManagerService $sixQPGameManagerService,
                                SPLGameManagerService $SPLGameManagerService,
                                GLMGameManagerService $GLMGameManagerService,
                                MYRGameManagerService $MYRGameManagerService)
    {
        $this->gameManagerServices[AbstractGameManagerService::$SIXQP_LABEL] = $sixQPGameManagerService;
        $this->gameManagerServices[AbstractGameManagerService::$SPL_LABEL] = $SPLGameManagerService;
        $this->gameManagerServices[AbstractGameManagerService::$GLM_LABEL] = $GLMGameManagerService;
        $this->gameManagerServices[AbstractGameManagerService::$MYR_LABEL] = $MYRGameManagerService;
    }

    public function createGame(string $gameName): int {
        return $this->gameManagerServices[$gameName]->createGame();
    }

    public function joinGame(int $gameId, User $user): int {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        return $this->gameManagerServices[$game->getGameName()]->createPlayer($user->getUsername(), $game);
    }

    public function quitGame(int $gameId, User $user): int
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
        if ($game == null) {
            $game = $this->gameGLMRepository->findOneBy(['id' => $gameId]);
        }
        if ($game == null) {
            $game = $this->gameMYRRepository->findOneBy(['id' => $gameId]);
        }
        return $game;
    }

}