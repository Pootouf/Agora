<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Entity\Game\Log;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\String_;

class LogService
{
    public static int $SYSTEM_ID = -1;
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * sendPlayerLog : register in database the action performed by the player during the game
     * @param : Game $game : the game in which the action occurred
     * @param : Player $player : the player who did the action
     * @param : String $message : the action performed
     */
    public function sendPlayerLog(Game $game, Player $player, String $message) : void
    {
        $this->sendLog($game->getId(), $player->getId(), $game->getGameName(), $message);
    }

    /**
     * sendSystemLog : register in database the action performed by the system during the game
     * @param : Game $game : the game in which the action occurred
     * @param : String $message : the action performed
     */
    public function sendSystemLog(Game $game, String $message) : void
    {
        $this->sendLog($game->getId(), $this::$SYSTEM_ID, $game->getGameName(), $message);
    }

    /**
     * sendLog : register in database the action performed by the player during the game
     * @param : Game $game : the game in which the action occurred
     * @param : Player $player : the player who did the action
     * @param : String $gameLabel : the name of the game
     * @param : String $message : the action performed
     */
    private function sendLog(int $gameId, int $playerId, String $gameLabel, String $message) : void
    {
        $log = new Log();
        $log->setGameId($gameId);
        $log->setPlayerId($playerId);
        $log->setGameLabel($gameLabel);
        $log->setMessage($message);
        $log->setDate(new DateTime());
        $log->setTime(new DateTime("now", new DateTimeZone('Europe/Paris')));
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}