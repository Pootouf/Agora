<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;

class GameService
{
    private array $playerRepositories;

    public function __construct(private readonly PlayerSixQPRepository $playerSixQPRepository,
                                private readonly PlayerSPLRepository $playerSPLRepository,
                                private readonly PlayerGLMRepository $playerGLMRepository,
                                private readonly PlayerMYRRepository $playerMYRRepository
    )
    {
        $this->playerRepositories[AbstractGameManagerService::$SIXQP_LABEL] = $this->playerSixQPRepository;
        $this->playerRepositories[AbstractGameManagerService::$SPL_LABEL] = $this->playerSPLRepository;
        $this->playerRepositories[AbstractGameManagerService::$GLM_LABEL] = $this->playerGLMRepository;
        $this->playerRepositories[AbstractGameManagerService::$MYR_LABEL] = $this->playerMYRRepository;
    }

    /**
     * getPlayerFromNameAndGame : return the player associated with a username and a game
     * @param Game $game
     * @param string  $name
     * @return ?Player
     */
    public function getPlayerFromNameAndGame(Game $game, String $name) : ?Player
    {
        $gameName = $game->getGameName();
        $repository = $this->playerRepositories[$gameName];
        return $repository->findOneBy(['game' => $game->getId(), 'username' => $name]);
    }
}