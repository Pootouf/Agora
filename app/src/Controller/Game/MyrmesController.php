<?php

namespace App\Controller\Game;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Service\Game\LogService;
use App\Service\Game\Myrmes\BirthMYRService;
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
                                private readonly LogService $logService,
                                private readonly BirthMYRService $birthMYRService) {}


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
            'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::$LARVAE_AREA)->count(),
            'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::$SOLDIERS_AREA)->count(),
            'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::$WORKER_AREA)->count()
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
            'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::$LARVAE_AREA)->count(),
            'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::$SOLDIERS_AREA)->count(),
            'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::$WORKER_AREA)->count()
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

    #[Route('/game/myrmes/{gameId}/placeNurse/{position}', name: 'app_game_myrmes_place_nurse')]
    public function placeNurse(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int $position
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
                $this->birthMYRService->placeNurse(
                    $this->service->getNursesAtPosition($player, MyrmesParameters::$BASE_AREA)->first(),
                    $position);
        } catch (Exception) {
            $message = $player->getUsername()
                . " a essayé de placer une nourrice sur la piste de naissance "
                . $position
                . " mais n'a pas pu";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("nurse not available for placing", Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a placé une nourrice sur une piste de naissance " . $position;
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("nurse placed on birth track " . $position, Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/confirmNursesPlacement', name: 'app_game_myrmes_confirm_nurses')]
    public function confirmNursesPlacement(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
            $this->birthMYRService->giveBirthBonus($player);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé de confirmé le placement de ses nourrices mais n'a pas pu";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to confirm nurses', Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a confirmé le placement de ses nourrices";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("nurses placement confirmed", Response::HTTP_OK);
    }
}