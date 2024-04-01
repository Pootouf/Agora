<?php

namespace App\Controller\Game;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Service\Game\LogService;
use App\Service\Game\Myrmes\EventMYRService;
use App\Service\Game\Myrmes\DataManagementMYRService;
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
                                private readonly DataManagementMYRService $dataManagementMYRService,
                                private readonly EventMYRService $eventMYRService,
                                private readonly LogService $logService) {}


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
            'isPreview' => true,
            'preys' => $game->getMainBoardMYR()->getPreys(),
            'isSpectator' => $player == null,
        ]);
    }

    #[Route('/game/myrmes/{id}/show/personalBoard', name: 'app_game_myrmes_show_personal_board')]
    public function showPersonalBoard(GameMYR $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());

        return $this->render('/Game/Myrmes/PersonalBoard/personalBoard.html.twig', [
            'player' => $player,
            'game' => $game,
            'preys' => $player->getPreyMYRs(),
            'isPreview' => false,
            'isSpectator' => $player == null,
        ]);
    }

    #[Route('/game/myrmes/{idGame}/displayPersonalBoard/{idPlayer}', name: 'app_game_myrmes_display_player_personal_board')]
    public function showPlayerPersonalBoard(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR,
        #[MapEntity(id: 'idPlayer')] PlayerMYR $playerMYR): Response
    {
        return $this->render('Game/Myrmes/PersonalBoard/playerPersonalBoard.html.twig',
            [
                'game' => $gameMYR,
                'player' => $playerMYR,
                'preys' => $playerMYR->getPreyMYRs(),
                'isPreview' => false,
                'isSpectator' => true,
            ]);
    }

    #[Route('/game/myrmes/{gameId}/up/bonus/', name: 'app_game_myrmes_up_bonus')]
    public function upBonus(
        #[MapEntity(id: 'idGame')] GameMYR $game,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
            $this->eventMYRService->upBonus($player);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé d'augmenter son bonus mais n'a pas pu";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("bonus is not reachable for player", Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a augmenté son bonus";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('Bonus upped', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/lower/bonus/', name: 'app_game_myrmes_lower_bonus')]
    public function lowerBonus(
        #[MapEntity(id: 'idGame')] GameMYR $game,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
            $this->eventMYRService->lowerBonus($player);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé d'abaisser son bonus mais n'a pas pu";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("bonus is not reachable for player", Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a baissé son bonus";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('Bonus lowered', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/confirm/bonus/', name: 'app_game_myrmes_confirm_bonus')]
    public function confirmBonus(
        #[MapEntity(id: 'idGame')] GameMYR $game,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $this->eventMYRService->confirmBonus($player);
        $message = $player->getUsername() . " a confirmé son choix de bonus";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('Bonus confirmed', Response::HTTP_OK);
    }
}