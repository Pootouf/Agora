<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\ORM\EntityManagerInterface;

class SPLService
{
    private EntityManagerInterface $entityManager;
    private PlayerSPLRepository $playerSPLRepository;

    public function __construct(EntityManagerInterface $entityManager, PlayerSPLRepository $playerSPLRepository)
    {
        $this->entityManager = $entityManager;
        $this->playerSPLRepository =  $playerSPLRepository;
    }

    /**
     * takeToken : player takes a token from the mainBoard
     * @param PlayerSPL $playerSPL
     * @param TokenSPL $tokenSPL
     * @return void
     */
    public function takeToken(PlayerSPL $playerSPL, TokenSPL $tokenSPL) : void
    {
        // Method to check player's tokens count

        // Method to check if player can take this token (2 same tokens method)

        // Method to check if player can take this token (3 different tokens method)

        // player can take and action is performed
    }

    public function isGameEnded(GameSPL $game, PlayerSPL $playerSPL) : bool
    {
        return true;
    }

    /**
     * @param GameSPL $game
     * @param string $name
     * @return ?PlayerSPL
     */
    public function getPlayerFromNameAndGame(GameSPL $game, string $name) : ?PlayerSPL
    {
        return $this->playerSPLRepository->findOneBy(['game' => $game->getId(), 'username' => $name]);
    }

    /**
     * @param Game $game
     * @return ?GameSPL
     */
    private function getGameSplFromGame(Game $game): ?GameSpl {
        /** @var GameSpl $game */
        return $game->getGameName() == AbstractGameManagerService::$SPL_LABEL ? $game : null;
    }
}