<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Entity\Game\GameUser;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\PlayerRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GameService
{
    private EntityManagerInterface $entityManager;
    private GameSixQPRepository $gameSixQPRepository;
    private SixQPService $sixQPService;

    public function __construct(EntityManagerInterface $entityManager,
                                GameSixQPRepository $gameSixQPRepository,
                                SixQPService $sixQPService)
    {
        $this->entityManager = $entityManager;
        $this->gameSixQPRepository = $gameSixQPRepository;
        $this->sixQPService = $sixQPService;
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
        $gameId = -1;
        switch ($gameName) {
            case "6QP":
                $gameId = $this->sixQPService->createSixQPGame();
        }
        return $gameId;
    }

    public function joinGame(int $gameId, GameUser $user): int {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        switch ($game->getGameName()) {
            case '6QP':
                /** @var GameSixQP $game */
                return $this->sixQPService->createSixQPPlayer($user->getUsername(), $game);
        }
        return -5;
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
        switch ($game->getGameName()) {
            case '6QP':
                /** @var GameSixQP $game */
                return $this->sixQPService->deleteSixQPPlayer($user->getUsername(), $game);
        }
        return -5;
    }

    public function deleteGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        switch ($game->getGameName()) {
            case '6QP':
                /** @var GameSixQP $game */
                $this->sixQPService->deleteGame($game);
                return 1;
        }
        return -5;
    }

    public function launchGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return -2;
        }
        switch ($game->getGameName()) {
            case '6QP':
                /** @var GameSixQP $game */
                return $this->sixQPService->launchGame($game);
        }
        return -5;
    }


    private function getGameFromId(int $gameId): ?Game {
        return $this->gameSixQPRepository->findOneBy(['id' => $gameId]);
    }

}