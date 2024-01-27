<?php

namespace App\Service\Game;

use AbstractGameService;
use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Entity\Game\GameUser;
use App\Repository\Game\PlayerRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class GameService
{
    private GameSixQPRepository $gameSixQPRepository;
    private array $services;

    public function __construct(GameSixQPRepository $gameSixQPRepository,
                                SixQPService $sixQPService)
    {
        $this->gameSixQPRepository = $gameSixQPRepository;
        $this->services[AbstractGameService::$SIXQP_LABEL] = $sixQPService;
    }

    public function getPlayerFromUser(?UserInterface $user,
                                      int $gameId,
                                      PlayerRepository $playerRepository): ?Player
    {
        if ($user == null) {
            return null;
        }
        $id = $user->getId(); //TODO : add platform user

        return $playerRepository->findOneBy(['id' => $id, 'game' => $gameId]);
    }

    public function createGame(string $gameName): int {
        return $this->services[$gameName]->createGame();
    }

    public function joinGame(int $gameId, GameUser $user): int {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        return $this->services[$game->getGameName()]->createPlayer($user->getUsername(), $game);
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
        return $this->services[$game->getGameName()]->deletePlayer($user->getUsername(), $game);
    }

    public function deleteGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        return $this->services[$game->getGameName()]->deleteGame($game);
    }

    public function launchGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        return $this->services[$game->getGameName()]->launchGame($game);
    }


    private function getGameFromId(int $gameId): ?Game {
        return $this->gameSixQPRepository->findOneBy(['id' => $gameId]);
    }

}