<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Repository\Game\SixQP\PlayerSixQPRepository;

class GameService
{
    private array $playerRepositories;

    public function __construct(private readonly PlayerSixQPRepository $playerSixQPRepository

    )
    {
        $this->playerRepositories[AbstractGameManagerService::$SIXQP_LABEL] = $this->playerSixQPRepository;
    }

    public function getPlayerFromNameAndGame(Game $game, String $name) : ?Player
    {
        $gameName = $game->getGameName();
        $repository = $this->playerRepositories[$gameName];
        return $repository->findOneBy(['game' => $game->getId(), 'username' => $name]);
    }
}