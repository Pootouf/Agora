<?php

namespace App\Service\Game;

use App\Entity\Game\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    private MessageRepository $messageRepository;
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * sendMessage : create a message and save it in the database
     * @param int $playerId the id of the player
     * @param int $gameId the id of the game
     * @param string $content the message to send
     * @return int 1 success, -1 message empty
     */
    public function sendMessage(int $playerId, int $gameId, string $content): int
    {
        if($content == null || $content == ""){
            return -1;
        }
        $message = new Message();
        $message->setDate(new \DateTimeImmutable());
        $message->setContent($content);
        $message->setAuthorId($playerId);
        $message->setGameId($gameId);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
        return 1;
    }

}