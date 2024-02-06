<?php

namespace App\Controller\Game;

use App\Service\Game\GameManagerService;
use App\Service\Game\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\HubInterface;

class MessageController extends AbstractController
{
    private HubInterface $hub;
    private EntityManagerInterface $entityManager;
    private MessageService $messageService;
    private GameManagerService $gameService;

    public function __construct(HubInterface $hub, EntityManagerInterface $entityManager,
                                GameManagerService$gameService, MessageService $messageService)
    {
        $this->hub = $hub;
        $this->entityManager = $entityManager;
        $this->messageService = $messageService;
        $this->gameService = $gameService;
    }

    public function sendMessage(int $playerId, int $gameId, string $message)
    {
        $this->messageService->sendMessage($playerId, $gameId, $message);
    }

    public function receiveMessage(int $gameId)
    {
        $this->messageService->receiveMessage($gameId);
    }
}
