<?php

namespace App\Service\Game;

use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class GameService
{
    public static int $NUMBER_OF_ROWS_BY_GAME = 4;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * createSixQPGame : create a six q p game with all the players in
     * @param array $players players to add in the game
     * @throws Exception if invalid number of player
     */
    public function createSixQPGame(array $players): GameSixQP
    {
        $numberOfPlayer = count($players);
        if (2 > $numberOfPlayer || $numberOfPlayer > 10) {
            throw new Exception("Invalid number of player");
        }

        $game = new GameSixQP();
        for($i = 0; $i < GameService::$NUMBER_OF_ROWS_BY_GAME; $i++) {
            $row = new RowSixQP();
            $row->setPosition($i);
            $game->addRowSixQP($row);
            $this->entityManager->persist($row);
        }

        for ($i = 0; $i < $numberOfPlayer; $i ++) {
            $this->createPlayer("Player".($i+1), $game);
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        //TODO: initialize the round with SixQPService

        return $game;
    }

    private function createPlayer(string $playerName, GameSixQP $game): void
    {
        $player = new PlayerSixQP($playerName, $game);
        $discard = new DiscardSixQP($player, $game);
        $player->setDiscardSixQP($discard);
        $game->addPlayerSixQP($player);
        $this->entityManager->persist($player);
        $this->entityManager->persist($discard);
    }

}