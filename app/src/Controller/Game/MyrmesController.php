<?php

namespace App\Controller\Game;

use App\Entity\Game\Myrmes\GameMYR;
use App\Service\Game\LogService;
use App\Service\Game\Myrmes\EventMYRService;
use App\Service\Game\Myrmes\MYRService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

/**
 * @codeCoverageIgnore
 */
class MyrmesController extends AbstractController
{

    public function __construct(private readonly MYRService $service,
                                private readonly EventMYRService $eventMYRService,
                                private readonly LogService $logService) {}


    #[Route('/game/myrmes/{id}', name: 'app_game_show_myr')]
    public function showGame(GameMYR $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());

        return $this->render('/Game/Myrmes/index.html.twig', [
            'player' => $player,
            'game' => $game,
            'tiles' => $game->getMainBoardMYR()->getTiles(),
            'preys' => $game->getMainBoardMYR()->getPreys()
        ]);
    }

    #[Route('/game/myrmes/{gameId}/choose/bonus/{bonusNb}', name: 'app_game_myrmes_choose_bonus')]
    public function chooseBonus(
        #[MapEntity(id: 'idGame')] GameMYR $game,
        int $bonus
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
            $this->eventMYRService->chooseBonus($player, $bonus);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé de choisir le bonus numéro " . $bonus . " mais n'a pas pu";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("bonus is not reachable for player", Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a choisi le bonus numéro " . $bonus;
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('Bonus chosen', Response::HTTP_OK);
    }
}