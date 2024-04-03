<?php

namespace App\Service\Game;

use App\Entity\Game\Message;
use App\Repository\Game\MessageRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    private MessageRepository $messageRepository;
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager,
                                MessageRepository $messageRepository)
    {
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
    }

    /**
     * sendMessage : create a message and save it in the database
     * @param int $playerId the id of the player
     * @param int $gameId the id of the game
     * @param string $authorUsername the pseudo of the player
     * @param string $content the message to send
     * @return int 1 success, -1 message empty
     */
    public function sendMessage(int $playerId, int $gameId, string $content, string $authorUsername): int
    {
        if (empty($content) || empty($authorUsername)) {
            return -1;
        }

        $message = new Message();
        $message->setDate(new DateTime("now"));
        $message->setContent($content);
        $message->setAuthorId($playerId);
        $message->setGameId($gameId);
        $message->setAuthorUsername($authorUsername);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
        return 1;
    }

    /**
     * receiveMessage : return an array of message from a game
     * @param int $gameId the id of the game
     * @return array<Message> of message from game
     */
    public function receiveMessage(int $gameId): array
    {
        return $this->messageRepository->findByGame($gameId);
    }

}
