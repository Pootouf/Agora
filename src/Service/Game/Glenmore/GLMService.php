<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class GLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private PlayerGLMRepository $playerGLMRepository)
    {}

    /**
     * getPlayerFromNameAndGame : return the player associated with a username and a game
     * @param GameGLM $game
     * @param string  $name
     * @return ?PlayerGLM
     */
    public function getPlayerFromNameAndGame(GameGLM $game, string $name): ?PlayerGLM
    {
        return $this->playerGLMRepository->findOneBy(['gameGLM' => $game->getId(), 'username' => $name]);
    }


    /**
     * getTilesFromGame : return the tiles from the board with the given game
     * @param GameGLM $game
     * @return Collection
     */
    public function getTilesFromGame(GameGLM $game): Collection
    {
        return $game->getMainBoard()->getBoardTiles();
    }
}