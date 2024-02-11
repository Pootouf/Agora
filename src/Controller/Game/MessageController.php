<?php

namespace App\Controller\Game;

use App\Service\Game\GameManagerService;
use App\Service\Game\MessageService;
use App\Service\Game\PublishService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private MessageService $messageService;
    private PublishService $publishService;

    public function __construct(MessageService $messageService,
                                PublishService $publishService)
    {
        $this->messageService = $messageService;
        $this->publishService = $publishService;
    }


    #[Route('/game/{gameId}/send/{playerId}/message/{message}', name: 'app_game_send_new_message')]
    public function sendMessage(int $playerId, int $gameId, string $message) : Response
    {
        $this->messageService->sendMessage($playerId, $gameId, $message);
        $this->publishMessage($gameId, $message, $playerId);
        return new Response('Message sent', Response::HTTP_OK);
    }

    #[Route('/game/{gameId}/display/chat', name: 'app_game_display_new_messages')]
    public function receiveMessage(int $gameId) : Response
    {
        $this->messageService->receiveMessage($gameId);
        return new Response(); //TODO: add chat template
    }

    private function publishMessage(int $gameId, string $message, int $playerId): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_display_new_messages', ['id' => $gameId]).'newMessage',
            new Response($message."§".$playerId));
    }
}
