<?php

namespace App\Controller\Game;

use App\Service\Game\GameService;
use App\Service\Game\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\HubInterface;

class MessageController extends AbstractController
{
    private HubInterface $hub;
    private EntityManagerInterface $entityManager;
    private MessageService $messageService;
    private GameService $gameService;

    public function __construct(HubInterface $hub, EntityManagerInterface $entityManager,
                                GameService$gameService, MessageService $messageService)
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

    public  function receiveMessage(int $gameId)
    {

    }
}