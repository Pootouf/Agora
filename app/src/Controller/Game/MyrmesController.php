<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\GameParameters;
use App\Entity\Game\DTO\GameTranslation;
use App\Entity\Game\DTO\Myrmes\BoardBoxMYR;
use App\Entity\Game\DTO\Myrmes\BoardTileMYR;
use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\MyrmesTranslation;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Service\Game\LogService;
use App\Service\Game\MessageService;
use App\Service\Game\Myrmes\BirthMYRService;
use App\Service\Game\Myrmes\EventMYRService;
use App\Service\Game\Myrmes\DataManagementMYRService;
use App\Service\Game\Myrmes\HarvestMYRService;
use App\Service\Game\Myrmes\MYRService;
use App\Service\Game\Myrmes\WinterMYRService;
use App\Service\Game\Myrmes\WorkerMYRService;
use App\Service\Game\Myrmes\WorkshopMYRService;
use App\Service\Game\PublishService;
use Doctrine\Common\Collections\Collection;
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
                                private readonly PublishService $publishService,
                                private readonly BirthMYRService $birthMYRService,
                                private readonly WorkerMYRService $workerMYRService,
                                private readonly HarvestMYRService $harvestMYRService,
                                private readonly WorkshopMYRService $workshopMYRService,
                                private readonly WinterMYRService $winterMYRService,
                                private readonly MessageService $messageService) {}


    #[Route('/game/myrmes/{id}', name: 'app_game_show_myr')]
    public function showGame(GameMYR $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());

        try {
            $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($game);
        } catch (Exception) {
            return new Response(MyrmesTranslation::RESPONSE_ERROR_CALCULATING_MAIN_BOARD,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messages = $this->messageService->receiveMessage($game->getId());

        return $this->render('/Game/Myrmes/index.html.twig', [
            'player' => $player,
            'game' => $game,
            'messages' => $messages,
            'goalsLevelOne' => $game->getMainBoardMYR()->getGameGoalsLevelOne(),
            'goalsLevelTwo' => $game->getMainBoardMYR()->getGameGoalsLevelTwo(),
            'goalsLevelThree' => $game->getMainBoardMYR()->getGameGoalsLevelThree(),
            'boardBoxes' => $boardBoxes,
            'isPreview' => true,
            'preys' => $game->getMainBoardMYR()->getPreys(),
            'isSpectator' => $player == null,
            'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
            'selectedBox' => null,
            'playerPhase' => $player== null ? $game->getPlayers()->first()->getPhase() : $player->getPhase(),
            'actualSeason' => $this->service->getActualSeason($game),
            'isAnotherPlayerBoard' => false,
            'availableLarvae' => $this->service->getAvailableLarvae($player),
            'isBirthPhase' => $this->service->isInPhase($player, MyrmesParameters::PHASE_BIRTH),
            'dirt' => $this->service->getPlayerResourceAmount(
                $player,
                MyrmesParameters::RESOURCE_TYPE_DIRT),
            'grass' => $this->service->getPlayerResourceAmount(
                $player,
                MyrmesParameters::RESOURCE_TYPE_GRASS),
            'stone' => $this->service->getPlayerResourceAmount(
                $player,
                MyrmesParameters::RESOURCE_TYPE_STONE),
            'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::LARVAE_AREA
            )->count(),
            'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::SOLDIERS_AREA
            )->count(),
            'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKER_AREA
            )->count(),
            'nursesOnWorkshop' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKSHOP_AREA
            )->count(),
            'nursesOnBaseArea' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::BASE_AREA
            )->count(),
            'sendingWorkerOnGarden' => false,
            'mustThrowResources' => $player != null
                && $this->service->isInPhase($player, MyrmesParameters::PHASE_WINTER)
                && $this->winterMYRService->mustDropResourcesForWinter($player),
            'hasFinishedObligatoryHarvesting' => $player == null
                || $this->harvestMYRService->areAllPheromonesHarvested($player),
            'canStillHarvest' => $player != null && $this->harvestMYRService->canStillHarvest($player),
            'hasSelectedAnthillHolePlacement' => false,
            'possibleAnthillHolePlacement' => $game->getGamePhase() == MyrmesParameters::PHASE_WORKSHOP ?
                $this->workshopMYRService->getAvailableAnthillHolesPositions($player)
                : null,
            'workersOnAnthillLevels' => $this->dataManagementMYRService
                ->workerOnAnthillLevels($player->getPersonalBoardMYR())
        ]);
    }

    #[Route('/game/myrmes/{id}/show/personalBoard', name: 'app_game_myrmes_show_personal_board')]
    public function showPersonalBoard(
        #[MapEntity(id: 'id')] GameMYR $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());

        return $this->render('/Game/Myrmes/PersonalBoard/personalBoard.html.twig', [
            'player' => $player,
            'game' => $game,
            'preys' => $player->getPreyMYRs(),
            'isPreview' => false,
            'isSpectator' => $player == null,
            'needToPlay' => $player->isTurnOfPlayer(),
            'isAnotherPlayerBoard' => false,
            'playerPhase' => $player->getPhase(),
            'availableLarvae' => $this->service->getAvailableLarvae($player),
            'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::LARVAE_AREA
            )->count(),
            'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::SOLDIERS_AREA
            )->count(),
            'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKER_AREA
            )->count(),
            'nursesOnWorkshop' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKSHOP_AREA
            )->count(),
            'nursesOnBaseArea' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::BASE_AREA
            )->count(),
            'mustThrowResources' => $player != null
                && $this->service->isInPhase($player, MyrmesParameters::PHASE_WINTER)
                && $this->winterMYRService->mustDropResourcesForWinter($player),
            'workersOnAnthillLevels' => $this->dataManagementMYRService
                ->workerOnAnthillLevels($player->getPersonalBoardMYR())
        ]);
    }

    #[Route('/game/myrmes/{idGame}/displayPersonalBoard/{idPlayer}',
        name: 'app_game_myrmes_display_player_personal_board')]
    public function showPlayerPersonalBoard(
        #[MapEntity(id: 'idGame')] GameMYR     $game,
        #[MapEntity(id: 'idPlayer')] PlayerMYR $playerMYR): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        return $this->render('Game/Myrmes/PersonalBoard/playerPersonalBoard.html.twig',
            [
                'game' => $game,
                'player' => $playerMYR,
                'preys' => $playerMYR->getPreyMYRs(),
                'isPreview' => false,
                'isSpectator' => true,
                'isAnotherPlayerBoard' => true,
                'playerPhase' => $playerMYR->getPhase(),
                'actualSeason' => $this->service->getActualSeason($game),
                'availableLarvae' => $this->service->getAvailableLarvae($playerMYR),
                'isBirthPhase' => $this->service->isInPhase($playerMYR, MyrmesParameters::PHASE_BIRTH),
                'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition(
                    $playerMYR,
                    MyrmesParameters::LARVAE_AREA
                )->count(),
                'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition(
                    $playerMYR, MyrmesParameters::SOLDIERS_AREA
                )->count(),
                'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition(
                    $playerMYR, MyrmesParameters::WORKER_AREA
                )->count(),
                'nursesOnWorkshop' => $this->service->getNursesAtPosition(
                    $playerMYR,
                    MyrmesParameters::WORKSHOP_AREA
                )->count(),
                'nursesOnBaseArea' => $this->service->getNursesAtPosition(
                    $playerMYR,
                    MyrmesParameters::BASE_AREA
                )->count(),
                'mustThrowResources' => false,
                'workersOnAnthillLevels' => $this->dataManagementMYRService
                    ->workerOnAnthillLevels($playerMYR->getPersonalBoardMYR())
            ]);
    }

    #[Route('/game/myrmes/{id}/display/mainBoard/box/{tileId}/actions',
        name: 'app_game_myrmes_display_main_board_box_actions')]
    public function displayMainBoardBoxActions(
        #[MapEntity(id: 'id')] GameMYR $game,
        #[MapEntity(id: 'tileId')] TileMYR $tile): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $boardBox = $this->dataManagementMYRService->createBoardBox($game, $tile, 0, 0);
        $this->publishHighlightTile($game, $player, $tile);
        return $this->returnDisplayBoardActions($game, $player, $boardBox, $tile);
    }

    #[Route('/game/myrmes/{id}/workerPhase/displayBoardBoxActions'
        .'/{antCoordX}/{antCoordY}/{shiftsCount}/{tileId}/{cleanedTiles}',
        name: 'app_game_myrmes_display_main_board_box_actions_worker_phase')]
    public function displayMainBoardBoxActionsWorkerPhase(
        #[MapEntity(id: 'id')] GameMYR $game,
        #[MapEntity(id: 'tileId')] TileMYR $tile,
        int $antCoordX,
        int $antCoordY,
        int $shiftsCount,
        string $cleanedTiles
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $coords = $this->dataManagementMYRService->getListOfCoordinatesFromString($cleanedTiles);
        $boardBox = $this->dataManagementMYRService->createBoardBoxWorkerPhase(
            $game, $tile, 0, 0,
            $antCoordY == $tile->getCoordY() && $antCoordX == $tile->getCoordX(),
            $player, $shiftsCount, $coords
        );
        $this->publishHighlightTile($game, $player, $tile);
        return $this->returnDisplayBoardActions($game, $player, $boardBox, $tile);
    }

    #[Route('/game/myrmes/{idGame}/display/objectives',
        name: 'app_game_myrmes_display_objectives')]
    public function displayObjectives(
        #[MapEntity(id: 'idGame')] GameMYR $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        return $this->render('Game/Myrmes/MainBoard/displayObjectives.html.twig', [
            'game' => $game,
            'goalsLevelOne' => $game->getMainBoardMYR()->getGameGoalsLevelOne(),
            'goalsLevelTwo' => $game->getMainBoardMYR()->getGameGoalsLevelTwo(),
            'goalsLevelThree' => $game->getMainBoardMYR()->getGameGoalsLevelThree(),
            'goalsAvailable' => null,
        ]);
    }

    #[Route('/game/myrmes/{idGame}/display/objectives/selection',
        name: 'app_game_myrmes_display_objective_selection')]
    public function displayObjectiveSelection(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        return $this->render('Game/Myrmes/MainBoard/displayObjectives.html.twig', [
            'game' => $gameMYR,
            'goalsLevelOne' => $gameMYR->getMainBoardMYR()->getGameGoalsLevelOne(),
            'goalsLevelTwo' => $gameMYR->getMainBoardMYR()->getGameGoalsLevelTwo(),
            'goalsLevelThree' => $gameMYR->getMainBoardMYR()->getGameGoalsLevelThree(),
            'goalsAvailable' => $this->workshopMYRService->playerAvailableGoals($player, $gameMYR),
            'stoneOrDirtGoal' => [
                'stone' => $this->workshopMYRService->getPlayerResourcesFromSelectedType(
                    $player, MyrmesParameters::RESOURCE_TYPE_STONE
                )->getQuantity(),
                'dirt' => $this->workshopMYRService->getPlayerResourcesFromSelectedType(
                    $player, MyrmesParameters::RESOURCE_TYPE_DIRT
                )->getQuantity()
            ]
        ]);
    }

    #[Route('/game/myrmes/{id}/display/personalBoard/throwResource/{playerResourceId}/actions',
        name: 'app_game_myrmes_display_throw_resource_actions')]
    public function displayThrowResourceActions(
        #[MapEntity(id: 'id')] GameMYR $game,
        #[MapEntity(id: 'playerResourceId')] PlayerResourceMYR $playerResourceMYR): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        return $this->render('Game/Myrmes/PersonalBoard/ActionsMenu/throwResourceMenu.html.twig', [
            'game' => $game,
            'player' => $player,
            'selectedPlayerResource' => $playerResourceMYR
        ]);

    }

    #[Route('/game/myrmes/{idGame}/up/bonus/', name: 'app_game_myrmes_up_bonus')]
    public function upBonus(
        #[MapEntity(id: 'idGame')] GameMYR $game,
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
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
        $this->publishPersonalBoard($game, $player);
        $this->publishPreview($game, $player);
        return new Response('Bonus upped', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{idGame}/lower/bonus/', name: 'app_game_myrmes_lower_bonus')]
    public function lowerBonus(
        #[MapEntity(id: 'idGame')] GameMYR $game,
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
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
        $this->publishPersonalBoard($game, $player);
        $this->publishPreview($game, $player);
        return new Response('Bonus lowered', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{idGame}/confirm/bonus/', name: 'app_game_myrmes_confirm_bonus')]
    public function confirmBonus(
        #[MapEntity(id: 'idGame')] GameMYR $game,
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        $this->eventMYRService->confirmBonus($player);
        $this->service->endPlayerRound($player);
        $this->service->setPhase($player, MyrmesParameters::PHASE_BIRTH);
        $this->publishNotification($game, MyrmesParameters::NOTIFICATION_DURATION_PHASE,
            MyrmesTranslation::WARNING,
            MyrmesTranslation::IMPOSSIBLE_TO_PLACE_NURSE, GameParameters::ALERT_NOTIFICATION_TYPE,
            GameParameters::NOTIFICATION_COLOR_RED, $player->getUsername());

        $message = $player->getUsername() . " a confirmé son choix de bonus";
        $this->logService->sendPlayerLog($game, $player, $message);
        $this->publishPersonalBoard($game, $player);
        return new Response('Bonus confirmed', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/placeNurse/{position}', name: 'app_game_myrmes_place_nurse')]
    public function placeNurse(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int $position
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->birthMYRService->placeNurse($player, $position);
        } catch (Exception) {
            $this->publishNotification($game, MyrmesParameters::NOTIFICATION_DURATION_PHASE,
                MyrmesTranslation::WARNING,
                MyrmesTranslation::IMPOSSIBLE_TO_PLACE_NURSE, GameParameters::ALERT_NOTIFICATION_TYPE,
                GameParameters::NOTIFICATION_COLOR_RED, $player->getUsername());
            $message = $player->getUsername()
                . " a essayé de placer une nourrice sur la piste de naissance "
                . $position
                . " mais n'a pas pu";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("nurse not available for placing", Response::HTTP_FORBIDDEN);
        }

        $message = $player->getUsername() . " a placé une nourrice sur une piste de naissance " . $position;
        $this->logService->sendPlayerLog($game, $player, $message);
        $this->publishPersonalBoard($game, $player);
        $this->publishPreview($game, $player);
        return new Response("nurse placed on birth track " . $position, Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/confirm/nursesPlacement', name: 'app_game_myrmes_confirm_nurses')]
    public function confirmNursesPlacement(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->birthMYRService->giveBirthBonus($player);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé de confirmé le placement de ses nourrices mais n'a pas pu";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to confirm nurses', Response::HTTP_FORBIDDEN);
        }

        $this->publishPreview($game, $player);
        $this->publishRanking($game, $player);
        $this->service->endPlayerRound($player);
        $this->service->setPhase($player, MyrmesParameters::PHASE_WORKER);

        $message = $player->getUsername() . " a confirmé le placement de ses nourrices";
        $this->logService->sendPlayerLog($game, $player, $message);
        $this->publishPersonalBoard($game, $player);
        $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($game);
        if ($game->getGamePhase() == MyrmesParameters::PHASE_WORKER) {
            foreach ($game->getPlayers() as $p) {
                $this->publishMainBoard($game, $p, $boardBoxes, false, false);
            }
        } else {
            $this->publishMainBoard($game, $player, $boardBoxes, false, false);
        }

        return new Response("nurses placement confirmed", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/cancel/nursesPlacement', name: 'app_game_myrmes_cancel_nurses')]
    public function cancelNursesPlacement(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        $this->birthMYRService->cancelNursesPlacement($player);

        $message = $player->getUsername() . " a annulé le placement de ses nourrices";
        $this->logService->sendPlayerLog($game, $player, $message);
        $this->publishPersonalBoard($game, $player);
        $this->publishPreview($game, $player);
        return new Response("nurses placement canceled", Response::HTTP_OK);
    }

    #[Route('/games/myrmes/{gameId}/selectAntHillHoleToSendWorker', name: 'app_game_myrmes_select_anthillhole')]
    public function selectAntHillHoleToSendWorker(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        try {
            $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($game,
                true, null, null, $player,
                $this->workerMYRService->getPlayerMovementPoints($player));
        } catch (Exception) {
            return new Response(MyrmesTranslation::RESPONSE_ERROR_CALCULATING_MAIN_BOARD,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->returnMainBoard($game, $player, $boardBoxes,
            null, true, false);
    }

    /**
     * @param GameMYR $game
     * @param int $antCoordX
     * @param int $antCoordY
     * @param int $shiftsCount
     * @param string $cleanedTiles is a formatted string with each tile coordinates
     *                      separated by a space and each coordinate separated by a _
     * @return Response
     */
    #[Route('/game/myrmes/{gameId}/workerPhase/mainBoard/{antCoordX}/{antCoordY}/{shiftsCount}/{cleanedTiles}',
    name: "app_game_worker_phase_display_main_board")]
    public function displayMainBoardDuringWorkerPhase(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int $antCoordX,
        int $antCoordY,
        int $shiftsCount,
        string $cleanedTiles
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $coords = $this->dataManagementMYRService->getListOfCoordinatesFromString($cleanedTiles);
        try {
            $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows(
                $game, true, $antCoordX, $antCoordY, $player, $shiftsCount, $coords);
        } catch (Exception) {
            return new Response(MyrmesTranslation::RESPONSE_ERROR_CALCULATING_MAIN_BOARD,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->returnMainBoard(
            $game, $player, $boardBoxes, null,
            false, false
        );
    }

    #[Route('/game/myrmes/{gameId}/placeWorkerOnColonyLevelTrack/{level}', name: 'app_game_myrmes_place_worker_colony')]
    public function placeWorkerOnColonyLevelTrack(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int $level
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if ($game->getGamePhase() != MyrmesParameters::PHASE_WORKER) {
            return new Response('Not in worker phase', Response::HTTP_FORBIDDEN);
        }

        try {
            $this->workerMYRService->placeAntInAnthill($player->getPersonalBoardMYR(), $level);
        } catch (Exception) {
            $message = $player->getUsername()
                . " a essayé de placer une ouvrière sur le niveau de fourmilière "
                . $level
                . MyrmesTranslation::NOT_ABLE;
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to place worker on colony', Response::HTTP_FORBIDDEN);
        }
        $this->publishPersonalBoard($game, $player);
        $this->publishPreview($game, $player);
        $this->publishRanking($game, $player);
        $this->service->setNextPlayerTurn($player);
        if ($this->service->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
            $this->service->setPhase($player, MyrmesParameters::PHASE_HARVEST);
        }

        $message = $player->getUsername() . " a placé une ouvrière sur le niveau de fourmilière " . $level;
        $this->logService->sendPlayerLog($game, $player, $message);

        foreach ($game->getPlayers() as $player) {
            if ($this->service->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
                $this->service->setPhase($player, MyrmesParameters::PHASE_HARVEST);
            }
        }
        return new Response("placed worker on colony", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/placeWorkerOnAntHillHole/{tileId}',
        name: 'app_game_myrmes_place_worker_anthillhole')]
    public function placeWorkerOnAntHillHole(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        #[MapEntity(id: 'tileId')] TileMYR $tile
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if ($game->getGamePhase() != MyrmesParameters::PHASE_WORKER) {
            return new Response('Not in worker phase', Response::HTTP_FORBIDDEN);
        }

        $anthillHole = $this->workshopMYRService->getAnthillHoleFromTile($tile, $game);
        try {
            $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $anthillHole);
        } catch (Exception $e) {
            $message = $player->getUsername()
                . " a essayé de sortir une ouvrière sur la tuile "
                . $tile->getId()
                . "  mais n'a pas pu.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("failed to place worker on garden :" . $e->getMessage(),
                Response::HTTP_FORBIDDEN);
        }

        $message = $player->getUsername() . " a placé une ouvrière sur sa sortie de fourmilière";
        $this->logService->sendPlayerLog($game, $player, $message);

        try {
            $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($game);
        } catch (Exception) {
            return new Response(MyrmesTranslation::RESPONSE_ERROR_CALCULATING_MAIN_BOARD,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $this->publishPersonalBoard($game, $player);
        return $this->returnMainBoard($game, $player, $boardBoxes,
            null, false, false);
    }

    #[Route('/game/myrmes/{gameId}/moveAnt/neededResources/soldierNb/{coordX}/{coordY}',
        name:'app_game_myrmes_needed_soldiers_to_move')]
    public function neededSoldiersToMove(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int                                $coordX,
        int                                $coordY
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        try {
            return new Response($this->workerMYRService->getNeededSoldiers($coordX, $coordY, $game, $player));
        } catch (Exception $e) {
            return new Response("can't get needed soldiers, invalid tile",
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/game/myrmes/{gameId}/moveAnt/neededResources/movementPoints/' .
        'originTile/{coordX1}/{coordY1}/destinationTile/{coordX2}/{coordY2}',
        name:'app_game_myrmes_needed_movement_points_to_move')]
    public function neededMovementPoints(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int                                $coordX1,
        int                                $coordY1,
        int                                $coordX2,
        int                                $coordY2
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        try {
            return new Response($this->workerMYRService
                ->getNeededMovementPoints($coordX1, $coordY1, $coordX2, $coordY2, $game, $player));
        } catch (Exception $e) {
            return new Response("can't get needed movement points, invalid tile",
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/game/myrmes/{gameId}/moveAnt/isValid/tile/{coordX}/{coordY}',
        name:'app_game_myrmes_is_valid_tile_to_move')]
    public function isValidTileToMoveAnt(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int                                $coordX,
        int                                $coordY
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $tile = $this->workerMYRService->getTileFromCoordinates($coordX, $coordY);
        try {
            return new Response($this->workerMYRService->isValidPositionForAnt($tile));
        } catch (Exception) {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/game/myrmes/{gameId}/moveAnt/canClean/pheromone/{coordX}/{coordY}/{playerDirtQuantity}',
        name:'app_game_myrmes_can_clean_pheromone')]
    public function canCleanPheromone(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int                                $coordX,
        int                                $coordY,
        int                                $playerDirtQuantity
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $tile = $this->workerMYRService->getTileFromCoordinates($coordX, $coordY);
        $pheromone = $this->workerMYRService->getPheromoneFromTile($game, $tile);
        if($tile != null) {
            return new Response($this->workerMYRService->canCleanPheromone($pheromone, $playerDirtQuantity));
        } else {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/game/myrmes/{gameId}/canPlace/pheromone/{coordX}/{coordY}/{tileType}/{orientation}/{cleanedTiles}',
        name:'app_game_myrmes_can_place_pheromone')]
    public function canPlacePheromone(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int                                $coordX,
        int                                $coordY,
        int                                $tileType,
        int                                $orientation,
        string                             $cleanedTiles
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $tile = $this->workerMYRService->getTileFromCoordinates($coordX, $coordY);
        $coords = $this->dataManagementMYRService->getListOfCoordinatesFromString($cleanedTiles);
        $type = $this->service->getTileTypeFromTypeAndOrientation($tileType, $orientation);
        if ($type == null) {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE_TYPE, Response::HTTP_FORBIDDEN);
        }
        if($tile != null) {
            return new Response($this->workerMYRService->canPlacePheromone($player, $tile, $type, $coords));
        } else {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/game/myrmes/{gameId}/moveAnt/clean/pheromone/{coordX}/{coordY}',
        name:'app_game_myrmes_clean_pheromone')]
    public function cleanPheromone(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int                                $coordX,
        int                                $coordY
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if ($game->getGamePhase() != MyrmesParameters::PHASE_WORKER) {
            return new Response('Not in worker phase', Response::HTTP_FORBIDDEN);
        }

        $tile = $this->workerMYRService->getTileFromCoordinates($coordX, $coordY);
        $pheromone = $this->workerMYRService->getPheromoneFromTile($game, $tile);
        if($tile == null) {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $this->workerMYRService->cleanPheromone($pheromone, $player);
        } catch (Exception ) {
            return new Response("cannot clean the pheromone",
                Response::HTTP_FORBIDDEN);
        }

        $this->service->setNextPlayerTurn($player);
        if ($this->service->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
            $this->service->setPhase($player, MyrmesParameters::PHASE_HARVEST);
        }

        return new Response("Pheromone cleaned");
    }

    #[Route('/game/myrmes/{gameId}/moveAnt/getPheromoneTiles/coords/givenTile/{coordX}/{coordY}',
        name:'app_game_myrmes_get_pheromone_tiles_coords')]
    public function getPheromoneTilesCoords(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int                                $coordX,
        int                                $coordY
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $tile = $this->workerMYRService->getTileFromCoordinates($coordX, $coordY);
        $pheromone = $this->workerMYRService->getPheromoneFromTile($game, $tile);

        if($tile != null) {
            return new Response($this->workerMYRService->getStringCoordsOfPheromoneTiles($pheromone));
        } else {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/game/myrmes/getTile/id/coords/{coordX}/{coordY}',
        name:'app_game_myrmes_get_tile_id_from_coords')]
    public function getTileIdFromCoords(
        int                                $coordX,
        int                                $coordY
    ): Response
    {
        $tile = $this->workerMYRService->getTileFromCoordinates($coordX, $coordY);
        if($tile == null) {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new Response($tile->getId(), Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/moveAnt/direction/{direction}', name:'app_game_myrmes_move_ant')]
    public function moveAnt(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int                                $direction

    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if ($game->getGamePhase() != MyrmesParameters::PHASE_WORKER) {
            return new Response('Not in worker phase', Response::HTTP_FORBIDDEN);
        }

        $ant = $player->getGardenWorkerMYRs()->first();
        if (!$ant) {
            return new Response("No ant to move", Response::HTTP_FORBIDDEN);
        }

        try {
            $this->workerMYRService->workerMove($player, $ant, $direction);
        } catch (Exception) {
            $message = $player->getUsername()
                . " a essayé de déplacer la fourmi dans la direction "
                . $direction
                . MyrmesTranslation::NOT_ABLE;
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to move ant in this direction', Response::HTTP_FORBIDDEN);
        }

        $message = $player->getUsername() . " a déplacé la fourmi ". $ant->getId() . "dans la direction " . $direction;
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("moved ant in this direction", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/confirm/action/workerPhase/', name: 'app_game_myrmes_confirm_action_worker_phase')]
    public function confirmActionWorkerPhase(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if ($game->getGamePhase() != MyrmesParameters::PHASE_WORKER) {
            return new Response('Not in worker phase', Response::HTTP_FORBIDDEN);
        }

        $this->workerMYRService->killPlayerGardenWorker($player);

        $this->service->setNextPlayerTurn($player);
        if ($this->service->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
            $this->service->setPhase($player, MyrmesParameters::PHASE_HARVEST);
        }

        foreach ($game->getPlayers() as $player) {
            if ($this->service->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
                $this->service->setPhase($player, MyrmesParameters::PHASE_HARVEST);
            }
        }

        return new Response("Action confirmed for worker phase", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/harvestResource/{tileId}', name: 'app_game_myrmes_harvest_resource')]
    public function harvestResource(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        #[MapEntity(id: 'tileId')] TileMYR $tileMYR
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->harvestMYRService->harvestPheromone($player, $tileMYR);
        } catch (Exception) {
            $message = $player->getUsername()
                . " a essayé de récolter une ressource sur la tuile "
                . $tileMYR->getId()
                . MyrmesTranslation::NOT_ABLE;
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to harvest resource on this tile', Response::HTTP_FORBIDDEN);
        }

        $message = $player->getUsername() . " a récolté la ressource sur la tuile " . $tileMYR->getId();
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("harvested resource on this tile", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/end/harvestPhase', name: 'app_game_myrmes_end_harvest_phase')]
    public function endHarvestPhase(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        if(!$this->harvestMYRService->areAllPheromonesHarvested($player)) {
            $message = $player->getUsername()
                . " a essayé de mettre fin à la phase de récolte "
                . " mais n'a pas pu car la récolte obligatoire n'est pas finie.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to end harvest phase', Response::HTTP_FORBIDDEN);
        }
        $this->service->endPlayerRound($player);

        if($this->service->canOnePlayerDoWorkshopPhase($game)) {
            $this->service->setPhase($player, MyrmesParameters::PHASE_WORKSHOP);
        } elseif($this->winterMYRService->canSetPhaseToWinter($game)) {
                $this->service->setPhase($player, MyrmesParameters::PHASE_WINTER);
        } else {
            $this->service->manageEndOfRound($game);
            $this->service->setPhase($player, MyrmesParameters::PHASE_EVENT);
        }

        $message = $player->getUsername()
            . " a mis fin à la phase de récolte ";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('ended harvest phase', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/workshop/activate/anthillHolePlacement',
        name: 'app_game_myrmes_activate_anthill_hole_placement')]
    public function activateAnthillHolePlacementWorkshop(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        try {
            $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($game);
        } catch (Exception ) {
            return new Response(MyrmesTranslation::RESPONSE_ERROR_CALCULATING_MAIN_BOARD,
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->returnMainBoard($game, $player, $boardBoxes,
            null, true, false);
    }

    #[Route('/game/myrmes/{gameId}/workshop/activate/anthillHolePlacement/{tileId}',
        name: 'app_game_myrmes_place_anthill_hole')]
    public function placeAnthillHoleWorkshop(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        #[MapEntity(id: 'tileId')] TileMYR $tileMYR
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->workshopMYRService->manageWorkshop($player,
                MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tileMYR);
        } catch (Exception $e) {
            $message = $player->getUsername()
                . " a essayé de poser un trou de fourmilière "
                . MyrmesTranslation::NOT_ABLE;
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to place anthill hole' . $e->getMessage(), Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername()
            . " a posé un trou de fourmilière "
            . " sur la tuile " . $tileMYR->getId();
        $this->logService->sendPlayerLog($game, $player, $message);
        $this->publishRanking($game, $player);
        return new Response('placed an anthill hole', Response::HTTP_OK);
    }

    #[Route('/Game/{gameId}/increaseAnthillLevel', name: 'app_game_myrmes_increase_anthill_level')]
    public function increaseAnthillLevel(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_LEVEL_AREA);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé d'augmenter son niveau de fourmilière mais n'a pas pû";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to increase anthill level', Response::HTTP_FORBIDDEN);
        }

        $message = $player->getUsername() . " a augmenté son niveau de fourmilière";
        $this->logService->sendPlayerLog($game, $player, $message);
        $this->publishRanking($game, $player);
        return new Response('increased anthill level', Response::HTTP_OK);
    }

    #[Route('/Game/{gameId}/createNurse', name: 'app_game_myrmes_create_nurse')]
    public function createNurse(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_NURSE_AREA);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé de créer une nourrice mais n'a pas pû.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("failed to create nurse", Response::HTTP_FORBIDDEN);
        }

        $message = $player->getUsername() . " a crée une nourrice.";
        $this->logService->sendPlayerLog($game, $player, $message);
        $this->publishRanking($game, $player);
        return new Response("created new nurse");
    }

    #[Route('/Game/{gameId}/confirmWorkshopActions', name: 'app_game_myrmes_confirm_workshop_actions')]
    public function confirmWorkshopActions(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if ($game->getGamePhase() != MyrmesParameters::PHASE_WORKSHOP) {
            return new Response("Not in phase workshop, can't end it", Response::HTTP_FORBIDDEN);
        }

        try {
            if ($this->winterMYRService->canSetPhaseToWinter($game)) {
                $player->setPhase(MyrmesParameters::PHASE_WINTER);
            } else {
                $player->setPhase(MyrmesParameters::PHASE_EVENT);
            }
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé de confirmer ses actions de l'atelier mais n'a pas pû.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("failed to confirm workshop actions", Response::HTTP_FORBIDDEN);
        }

        if (!$this->service->canOnePlayerDoWorkshopPhase($game)) {
            foreach ($game->getPlayers() as $player) {
                if($this->winterMYRService->canSetPhaseToWinter($game)) {
                    $this->service->setPhase($player, MyrmesParameters::PHASE_WINTER);
                } else {
                    $this->service->setPhase($player, MyrmesParameters::PHASE_EVENT);
                }
            }
            return new Response('ended harvest phase', Response::HTTP_OK);
        }

        $message = $player->getUsername() . " a confirmé ses actions de l'atelier.";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("confirmed workshop actions", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/throwResource/warehouse/{playerResourceId}',
        name: 'app_game_myrmes_throw_resource_warehouse')]
    public function throwResourceFromWarehouse(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        #[MapEntity(id: 'playerResourceId')] PlayerResourceMYR $playerResourceMYR
    ): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if ($game->getGamePhase() != MyrmesParameters::PHASE_WINTER) {
            return new Response("Not in phase winter, can't throw resources", Response::HTTP_FORBIDDEN);
        }

        try {
            $this->winterMYRService->removeCubeOfWarehouse($player, $playerResourceMYR);
        } catch (Exception) {
            $message = $player->getUsername()
                . " a essayé de jeter la ressource "
                . $playerResourceMYR->getId()
                . " de son entrepôt, mais n'a pas pu.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to throw this resource from warehouse',
                Response::HTTP_FORBIDDEN);
        }

        $message = $player->getUsername() . " a jeté la ressource " . $playerResourceMYR->getId() . " de son entrepôt";
        $this->logService->sendPlayerLog($game, $player, $message);

        if($this->winterMYRService->canManageEndOfWinter($game)) {
            try {
                $this->winterMYRService->manageEndOfWinter($game);
            } catch (Exception) {
                $message =
                    "Le système a essayé de terminer la phase hiver "
                    . ", mais n'a pas pu.";
                $this->logService->sendSystemLog($game, $message);
                return new Response('failed to manage end of winter', Response::HTTP_FORBIDDEN);
            }
            $message = "Le système a mis fin à la phase hiver ";
            $this->logService->sendSystemLog($game, $message);
            foreach ($game->getPlayers() as $player) {
                $this->service->setPhase($player, MyrmesParameters::PHASE_EVENT);
            }
        }

        return new Response("threw resource from warehouse", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{idGame}/display/menu/pheromone/{tileId}',
        name: 'app_game_myrmes_display_pheromone_special_tile_menu_to_place')]
    public function displayPheromoneAndSpecialTileMenuToPlace(
        #[MapEntity(id: 'idGame')] GameMYR $game,
        #[MapEntity(id: 'tileId')] TileMYR $tileMYR
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }

        return $this->render('Game/Myrmes/MainBoard/displayObjectPlacement.html.twig', [
            'game' => $game,
            'player' => $player,
            'tile' => $tileMYR,
            'tiles' => $this->workerMYRService->getAvailablePheromones($player)
        ]);
    }

    #[Route('/game/myrmes/{idGame}/displayMainBoardTilePositionPossibilities/{tileId}/{tileType}/{orientation}'
        . '/{antCoordX}/{antCoordY}/{cleanedTilesString}',
        name: 'app_game_myrmes_display_mainBoard_tile_position_possibilities')]
    public function displayMainBoardTilePositionPossibilities(
        #[MapEntity(id: 'idGame')] GameMYR  $game,
        #[MapEntity(id: 'tileId')] TileMYR  $tileMYR,
        #[MapEntity(id: 'tileType')] int    $tileType,
        #[MapEntity(id: 'orientation')] int $orientation,
        int $antCoordX, int $antCoordY, string $cleanedTilesString
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $tileTypeMYR = $this->service->getTileTypeFromTypeAndOrientation($tileType, $orientation);
        if ($tileTypeMYR == null) {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE_TYPE, Response::HTTP_FORBIDDEN);
        }
        $cleanedTiles = $this->dataManagementMYRService->getListOfCoordinatesFromString($cleanedTilesString);

        try {
            $positions = $this->workerMYRService->getAllAvailablePositions(
                $player, $tileMYR, $tileTypeMYR,
                $antCoordX, $antCoordY, $cleanedTiles
            );
        }catch (Exception $e) {
            return new Response("No positions available for this tile : " . $e->getMessage(),
                Response::HTTP_FORBIDDEN);
        }

        $answer = "";
        foreach ($positions as $position) {
            /**
             * @var BoardTileMYR $pivotPoint
             */
            $pivotPoint = $position->findFirst(function (int $key, BoardTileMYR $value) {
                return $value->isPivot();
            });
            $position->removeElement($pivotPoint);
            $otherTiles = [];

            /**
             * @var BoardTileMYR $boardTile
             */
            foreach ($position as $boardTile) {
                $otherTiles[] = $boardTile->getTile()->getId();
            }
            $answer .= $pivotPoint->getTile()->getId() . "__" . implode("_", $otherTiles) . "___";
        }
        $answer = substr($answer, 0, strlen($answer)-3);

        return new Response($answer);
    }

    #[Route('/game/myrmes/{idGame}/placePheromone/{tileId}/{tileType}/{orientation}',
        name: 'app_game_myrmes_place_pheromone')]
    public function placePheromone(
        #[MapEntity(id: 'idGame')] GameMYR  $game,
        #[MapEntity(id: 'tileId')] TileMYR  $tileMYR,
        #[MapEntity(id: 'tileType')] int    $tileType,
        #[MapEntity(id: 'orientation')] int $orientation,
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if (!$player->isTurnOfPlayer()) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        $tileTypeMYR = $this->service->getTileTypeFromTypeAndOrientation($tileType, $orientation);
        if ($tileTypeMYR == null) {
            return new Response(MyrmesTranslation::RESPONSE_INVALID_TILE_TYPE, Response::HTTP_FORBIDDEN);
        }

        try {
            $this->workerMYRService->placePheromone($player, $tileMYR, $tileTypeMYR);
        }catch (Exception $e) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . " a essayé de placer la phéromone de type " . $tileTypeMYR->getType()
                . " sur la tuile " . $tileMYR->getId() . "alors qu'il ne peut pas");
            return new Response("Can't place the pheromone : " . $e->getMessage(),
                Response::HTTP_FORBIDDEN);
        }

        $this->service->setNextPlayerTurn($player);
        if ($this->service->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
            $this->service->setPhase($player, MyrmesParameters::PHASE_HARVEST);
        }

        foreach ($game->getPlayers() as $player) {
            if ($this->service->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
                $this->service->setPhase($player, MyrmesParameters::PHASE_HARVEST);
            }
        }

        return new Response('Pheromone positioned', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/sacrifice/larvae', name: 'app_game_myrmes_sacrifice_larvae')]
    public function sacrificeLarvae(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        try {
            $this->service->exchangeLarvaeForFood($player);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé de sacrifier 3 larves mais n'a pas pû.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("can't sacrifice larvae", Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a réussi a sacrifier 3 de ses larves.";
        $this->logService->sendPlayerLog($game, $player, $message);
        $this->publishPreview($game, $player);
        $this->publishRanking($game, $player);
        $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($game);
        $this->publishMainBoard($game, $player, $boardBoxes, false, false);
        return new Response('larvae successfully sacrificed', Response::HTTP_OK);
    }

    /**
     * returnMainBoard : return the response with the given parameters for main board
     * @param GameMYR $game
     * @param PlayerMYR|null $player
     * @param Collection $boardBoxes
     * @param BoardBoxMYR|null $selectedBox
     * @param bool $sendingWorkerOnGarden
     * @param bool $hasSelectedAnthillHolePlacement
     * @return Response
     */
    private function returnMainBoard(GameMYR $game, ?PlayerMYR $player, Collection $boardBoxes,
                                     ?BoardBoxMYR $selectedBox, bool $sendingWorkerOnGarden,
                                     bool $hasSelectedAnthillHolePlacement ) : Response
    {
        return $this->render('/Game/Myrmes/MainBoard/mainBoard.html.twig', [
            'player' => $player,
            'game' => $game,
            'boardBoxes' => $boardBoxes,
            'isSpectator' => $player == null,
            'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
            'selectedBox' => $selectedBox,
            'playerPhase' => $player->getPhase(),
            'actualSeason' => $this->service->getActualSeason($game),
            'sendingWorkerOnGarden' => $sendingWorkerOnGarden,
            'nursesOnWorkshop' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKSHOP_AREA
            )->count(),
            'hasSelectedAnthillHolePlacement' => $hasSelectedAnthillHolePlacement,
            'availableLarvae' => $this->service->getAvailableLarvae($player)
        ]);
    }

    /**
     * returnDisplayBoardActions : return the response with the given parameters for display board actions
     * @param GameMYR $game
     * @param PlayerMYR|null $player
     * @param BoardBoxMYR $boardBox
     * @param TileMYR $tile
     * @return Response
     */
    private function returnDisplayBoardActions(GameMYR $game,
                                               ?PlayerMYR $player,
                                               BoardBoxMYR $boardBox,
                                               TileMYR $tile
    ): Response
    {
        return $this->render('Game/Myrmes/MainBoard/displayBoardBoxActions.html.twig', [
            'game' => $game,
            'player' => $player,
            'selectedBox' => $boardBox,
            'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
            'playerPhase' => $player->getPhase(),
            'hasFinishedObligatoryHarvesting' => $this->harvestMYRService->areAllPheromonesHarvested($player),
            'canStillHarvest' => $this->harvestMYRService->canStillHarvest($player),
            'tile' => $tile,
            'ant' => $player->getGardenWorkerMYRs()->first()
        ]);
    }

    /**
     * returnPersonalBoard: return the response with the given parameters for personal board
     * @param GameMYR $game
     * @param PlayerMYR $player
     * @return Response
     */
    private function returnPersonalBoard(GameMYR $game,PlayerMYR $player) : Response
    {
        return $this->render('Game/Myrmes/PersonalBoard/personalBoard.html.twig', [
            'game' => $game,
            'player' => $player,
            'preys' => $player->getPreyMYRs(),
            'isPreview' => false,
            'isSpectator' => $player == null,
            'needToPlay' => $player->isTurnOfPlayer(),
            'isAnotherPlayerBoard' => false,
            'playerPhase' => $player->getPhase(),
            'availableLarvae' => $this->service->getAvailableLarvae($player),
            'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::LARVAE_AREA
            )->count(),
            'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::SOLDIERS_AREA
            )->count(),
            'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKER_AREA
            )->count(),
            'nursesOnWorkshop' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKSHOP_AREA
            )->count(),
            'nursesOnBaseArea' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::BASE_AREA
            )->count(),
            'mustThrowResources' => $this->service->isInPhase($player, MyrmesParameters::PHASE_WINTER)
                && $this->winterMYRService->mustDropResourcesForWinter($player),
            'workersOnAnthillLevels' => $this->dataManagementMYRService
                ->workerOnAnthillLevels($player->getPersonalBoardMYR())
        ]);
    }

    /**
     * returnPreview: return the response with the given parameters for preview
     * @param GameMYR $game
     * @param PlayerMYR $player
     * @return Response
     */
    private function returnPreview(GameMYR $game, PlayerMYR $player) : Response
    {
        return $this->render('Game/Myrmes/MainBoard/preview.html.twig', [
            'player' => $player,
            'game' => $game,
            'isPreview' => true,
            'isAnotherPlayerBoard' => false,
            'playerPhase' => $player->getPhase(),
            'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
            'availableLarvae' => $this->service->getAvailableLarvae($player),
            'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::LARVAE_AREA
            )->count(),
            'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::SOLDIERS_AREA
            )->count(),
            'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKER_AREA
            )->count(),
            'nursesOnWorkshop' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::WORKSHOP_AREA
            )->count(),
            'nursesOnBaseArea' => $this->service->getNursesAtPosition(
                $player,
                MyrmesParameters::BASE_AREA
            )->count(),
            'workersOnAnthillLevels' => $this->dataManagementMYRService
                ->workerOnAnthillLevels($player->getPersonalBoardMYR())
        ]);
    }

    /**
     * returnRanking return the response with the given parameters for ranking
     * @param GameMYR $game
     * @param PlayerMYR $player
     * @return Response
     */
    private function returnRanking(GameMYR $game, PlayerMYR $player) : Response
    {
        return $this->render('Game/Myrmes/Ranking/ranking.html.twig', [
            'player' => $player,
            'game' => $game,
            'isSpectator' => $player == null
        ]);
    }

    /**
     * publishMainBoard: publish with mercure the main board
     * @param GameMYR $game
     * @param BoardBoxMYR $selectedBox
     * @param Collection $boardBoxes
     * @param bool $sendingWorkerOnGarden
     * @param bool $hasSelectedAnthillHolePlacement
     * @return void
     */
    private function publishMainBoard(GameMYR $game, PlayerMYR $player, Collection $boardBoxes,
                                      bool $sendingWorkerOnGarden, bool $hasSelectedAnthillHolePlacement ) : void
    {
        $response = $this->returnMainBoard(
            $game, $player, $boardBoxes, null, $sendingWorkerOnGarden, $hasSelectedAnthillHolePlacement
        );
        $this->publishService->publish(
            $this->generateUrl('app_game_show_myr',
                ['id' => $game->getId()]).'mainBoard'.$player->getId(),
            $response
        );
    }

    /**
     * @param GameMYR $game
     * @param PlayerMYR $player
     * @param TileMYR $tile
     * @return void
     */
    private function publishHighlightTile(GameMYR $game, PlayerMYR $player, TileMYR $tile) : void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_myr',
                ['id' => $game->getId()]).'highlight'.$player->getId(),
            new Response($tile->getId())
        );
    }

    /**
     * publishPersonalBoard: publish personal board with mercure
     * @param GameMYR $game
     * @param PlayerMYR $player
     * @return void
     */
    private function publishPersonalBoard(GameMYR $game, PlayerMYR $player) : void
    {
        $response = $this->returnPersonalBoard($game, $player);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_myr',
                ['id' => $game->getId()]).'personalBoard'.$player->getId(),
            $response
        );
    }

    /**
     * publishPreview: publish preview with mercure
     * @param GameMYR $game
     * @param PlayerMYR $player
     * @return void
     */
    private function publishPreview(GameMYR $game, PlayerMYR $player ) : void
    {
        $response = $this->returnPreview($game, $player);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_myr',
                ['id' => $game->getId()]).'preview'.$player->getId(),
            $response);
    }

    /**
     * publishRanking: publish ranking with mercure
     * @param GameMYR $game
     * @param PlayerMYR $player
     * @return void
     */
    private function publishRanking(GameMYR $game, PlayerMYR $player) : void
    {
        $response = $this->returnRanking($game, $player);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_myr',
                ['id' => $game->getId()]).'ranking'.$player->getId(),
            $response);
    }

    #[Route('/game/myrmes/{idGame}/displayStoneDirtGoal/{goalId}',
        name: 'app_game_myrmes_display_stone_dirt_goal')]
    public function displayStoneDirtGoal(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR,
        #[MapEntity(id: 'goalId')] GameGoalMYR $gameGoalMYR
    ): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }

        $quantityNeeded =
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE == $gameGoalMYR->getGoal()->getDifficulty() ?
            MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_OR_DIRT_LEVEL_ONE :
            MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_OR_DIRT_LEVEL_THREE;

        return $this->render('Game/Myrmes/MainBoard/InteractiveGoals/stoneOrDirtGoal.html.twig', [
            'game' => $gameMYR,
            'goal' => $gameGoalMYR,
            'stoneQuantity' => $this->workshopMYRService->getPlayerResourcesFromSelectedType(
                $player, MyrmesParameters::RESOURCE_TYPE_STONE
            )->getQuantity(),
            'dirtQuantity' => $this->workshopMYRService->getPlayerResourcesFromSelectedType(
                $player, MyrmesParameters::RESOURCE_TYPE_DIRT
            )->getQuantity(),
            'totalQuantityNeeded' => $quantityNeeded
        ]);
    }

    #[Route('/game/myrmes/{idGame}/validateGoal/{goalId}',
        name: 'app_game_myrmes_validate_goal')]
    public function validateGoal(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR,
        #[MapEntity(id: 'goalId')] GameGoalMYR $gameGoalMYR,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $nurse = $this->service->getNursesAtPosition($player, MyrmesParameters::WORKSHOP_AREA)->first();
        if (!$nurse) {
            return new Response('No nurse available in workshop', Response::HTTP_FORBIDDEN);
        }

        try {
            $this->workshopMYRService->doGoal($player, $gameGoalMYR, $nurse);
        }catch (Exception $e){
            return new Response($e->getMessage(), Response::HTTP_FORBIDDEN);
        }

        return new Response('Goal validated', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{idGame}/validateGoal/{goalId}/stone/{stoneQuantity}/dirt/{dirtQuantity}',
        name: 'app_game_myrmes_validate_stone_or_dirt_goal')]
    public function validateStoneOrDirtGoal(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR,
        #[MapEntity(id: 'goalId')] GameGoalMYR $gameGoalMYR,
        #[MapEntity(id: 'stoneQuantity')] int $stoneQuantity,
        #[MapEntity(id: 'dirtQuantity')] int $dirtQuantity,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $nurse = $this->service->getNursesAtPosition($player, MyrmesParameters::WORKSHOP_AREA)->first();
        if (!$nurse) {
            return new Response('No nurse available in workshop', Response::HTTP_FORBIDDEN);
        }

        try {
            $this->workshopMYRService->doStoneOrDirtGoal($player, $gameGoalMYR, $nurse, $stoneQuantity, $dirtQuantity);
        }catch (Exception $e){
            return new Response($e->getMessage(), Response::HTTP_FORBIDDEN);
        }

        return new Response('Goal validated', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{idGame}/validateGoal/{goalId}/pheromones/{pheromoneIds}',
        name: 'app_game_myrmes_validate_pheromone_goal')]
    public function validatePheromoneGoal(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR,
        #[MapEntity(id: 'goalId')] GameGoalMYR $gameGoalMYR,
        #[MapEntity(id: 'pheromoneIds')] String $pheromoneIds,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $nurse = $this->service->getNursesAtPosition($player, MyrmesParameters::WORKSHOP_AREA)->first();
        if (!$nurse) {
            return new Response('No nurse available in workshop', Response::HTTP_FORBIDDEN);
        }
        $pheromoneIds = explode('_', $pheromoneIds);
        if (empty($pheromoneIds)) {
            return new Response('No pheromone ids given', Response::HTTP_FORBIDDEN);
        }

        try {
            $pheromones = $this->service->getPheromonesFromListOfIds($pheromoneIds);
            $this->workshopMYRService->doPheromoneGoal($player, $gameGoalMYR, $nurse, $pheromones);
        }catch (Exception $e){
            return new Response($e->getMessage(), Response::HTTP_FORBIDDEN);
        }

        return new Response('Goal validated', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{idGame}/validateGoal/{goalId}/stone/{stoneQuantity}/dirt/{dirtQuantity}',
        name: 'app_game_myrmes_validate_special_tile_goal')]
    public function validateSpecialTileGoal(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR,
        #[MapEntity(id: 'goalId')] GameGoalMYR $gameGoalMYR,
        #[MapEntity(id: 'specialTileIds')] String $specialTileIds,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $nurse = $this->service->getNursesAtPosition($player, MyrmesParameters::WORKSHOP_AREA)->first();
        if (!$nurse) {
            return new Response('No nurse available in workshop', Response::HTTP_FORBIDDEN);
        }

        $specialTileIds = explode('_', $specialTileIds);
        $specialTiles = $this->service->getPheromonesFromListOfIds($specialTileIds);
        try {
            $this->workshopMYRService->doSpecialTileGoal($player, $gameGoalMYR, $nurse, $specialTiles);
        }catch (Exception $e){
            return new Response($e->getMessage(), Response::HTTP_FORBIDDEN);
        }

        return new Response('Goal validated', Response::HTTP_OK);
    }

    private function publishNotification(GameMYR $game, int $duration, string $message,
                                         string $description, string $iconId,
                                         string $loadingBarColor, string $targetedPlayer): void
    {
        $dataSent =  [$duration, $message, $description, $iconId, $loadingBarColor];

        $this->publishService->publish(
            $this->generateUrl('app_game_show_myr',
                ['id' => $game->getId()]).'notification'.$targetedPlayer,
            new Response(implode('_', $dataSent))
        );
    }
}