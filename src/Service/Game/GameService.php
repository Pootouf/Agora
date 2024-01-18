<?php

namespace App\Service\Game;

use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class GameService
{
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
    public function createSixQPGame(array $players): void
    {
        $numberOfPlayer = count($players);
        if (2 > $numberOfPlayer || $numberOfPlayer > 10) {
            throw new Exception("Invalid number of player");
        }

        $game = new GameSixQP();
        $this->entityManager->persist($game);

        for ($i = 0; $i < $numberOfPlayer; $i ++) {
            $this->createPlayer("Player".($i+1), $game);
        }

        $this->entityManager->flush();
    }

    private function createPlayer(string $playerName, GameSixQP $game): void
    {
        $player = new PlayerSixQP($playerName, $game);
        $discard = new DiscardSixQP($player, $game);
        $player->setDiscardSixQP($discard);
        $this->entityManager->persist($player);
        $this->entityManager->persist($discard);
    }

}