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

    public function __construct(
        MessageService $messageService,
        PublishService $publishService
    ) {
        $this->messageService = $messageService;
        $this->publishService = $publishService;
    }


    #[Route('/game/{gameId}/message/send/{playerId}/{authorUsername}/{message}', name: 'app_game_send_new_message')]
    public function sendMessage(int $playerId, int $gameId, string $message, string $authorUsername): Response
    {
        $this->messageService->sendMessage($playerId, $gameId, $message, $authorUsername);
        $this->publishMessage($gameId, $message, $authorUsername);
        return new Response('Message sent', Response::HTTP_OK);
    }

    #[Route('/game/{gameId}/message/display', name: 'app_game_display_new_messages')]
    public function receiveMessage(int $gameId): Response
    {
        $messages = $this->messageService->receiveMessage($gameId);
        return $this->render('Game/Utils/chat.html.twig', [
            'messages' => $messages
        ]);
    }

    private function publishMessage(int $gameId, string $message, string $playerUsername): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_display_new_messages', ['gameId' => $gameId]).'newMessage',
            new Response($message."§".$playerUsername)
        );
    }
}
