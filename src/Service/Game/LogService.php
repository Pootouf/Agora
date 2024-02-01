<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Entity\Game\Log;
use Doctrine\ORM\EntityManagerInterface;

class LogService
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * sendLog : register in database the action performed by the player during the game
     * @param : Game $game : the game in which the action occurred
     * @param : Player $player : the player who did the action
     * @param : String $message : the action performed
     */
    public function sendLog(Game $game, Player $player, String $message) : void
    {
        $log = new Log();
        $log->setGameId($game->getId());
        $log->setPlayerId($player->getId());
        $log->setMessage($message);
        $log->setDate(new \DateTimeImmutable());
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}