<?php

namespace App\Controller\Game;

use App\Entity\Game\Myrmes\GameMYR;
use App\Service\Game\Myrmes\DataManagementMYRService;
use App\Service\Game\Myrmes\MYRService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyrmesController extends AbstractController
{

    public function __construct(private readonly MYRService $service,
                                private readonly DataManagementMYRService $dataManagementMYRService)
    {

    }

    #[Route('/game/myrmes/{id}', name: 'app_game_show_myr')]
    public function showGame(GameMYR $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());

        $boardBoxes = null;
        try {
            $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($game);
        } catch (\Exception $e) {
            return new Response("Error while calculating main board tiles disposition",
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->render('/Game/Myrmes/index.html.twig', [
            'player' => $player,
            'game' => $game,
            'boardBoxes' => $boardBoxes,
            'preys' => $game->getMainBoardMYR()->getPreys(),
            'isSpectator' => $player == null,
        ]);
    }

    #[Route('/game/myrmes/{id}/show/mainBoard', name: 'app_game_myrmes_show_main_board')]
    public function showMainBoard(GameMYR $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());

        $boardBoxes = null;
        try {
            $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($game);
        } catch (\Exception $e) {
            return new Response("Error while calculating main board tiles disposition",
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->render('/Game/Myrmes/MainBoard/mainBoard.html.twig', [
            'player' => $player,
            'game' => $game,
            'boardBoxes' => $boardBoxes,
            'preys' => $game->getMainBoardMYR()->getPreys(),
            'isSpectator' => $player == null,
        ]);
    }
}