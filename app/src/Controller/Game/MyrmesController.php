<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\Myrmes\BoardBoxMYR;
use App\Entity\Game\DTO\Myrmes\BoardTileMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Service\Game\LogService;
use App\Service\Game\Myrmes\BirthMYRService;
use App\Service\Game\Myrmes\EventMYRService;
use App\Service\Game\Myrmes\DataManagementMYRService;
use App\Service\Game\Myrmes\HarvestMYRService;
use App\Service\Game\Myrmes\MYRService;
use App\Service\Game\Myrmes\WinterMYRService;
use App\Service\Game\Myrmes\WorkerMYRService;
use App\Service\Game\Myrmes\WorkshopMYRService;
use App\Service\Game\PublishService;
use Psr\Log\LoggerInterface;
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
                                private readonly WinterMYRService $winterMYRService) {}


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
            'needToPlay' => true,//$player == null ? false : $player->isTurnOfPlayer(),
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
            'mustThrowResources' => $player != null
                && $this->service->isInPhase($player, MyrmesParameters::PHASE_WINTER)
                && $this->winterMYRService->mustDropResourcesForWinter($player),
            'hasFinishedObligatoryHarvesting' => !($player != null)
                || $this->harvestMYRService->areAllPheromonesHarvested($player),
            'canStillHarvest' => $player != null && $this->harvestMYRService->canStillHarvest($player),
            'hasSelectedAnthillHolePlacement' => false,
            'possibleAnthillHolePlacement' => $game->getGamePhase() == MyrmesParameters::PHASE_WORKSHOP ?
                $this->workshopMYRService->getAvailableAnthillHolesPositions($player)
                : null,
        ]);
    }

    #[Route('/game/myrmes/{id}/show/personalBoard', name: 'app_game_myrmes_show_personal_board')]
    public function showPersonalBoard(
        #[MapEntity(id: 'id')] GameMYR $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());

        return $this->render('/Game/Myrmes/PersonalBoard/personalBoard.html.twig', [
            'player' => $player,
            'game' => $game,
            'preys' => $player->getPreyMYRs(),
            'isPreview' => false,
            'isSpectator' => $player == null,
            'needToPlay' => true,//$player == null ? false : $player->isTurnOfPlayer()
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
            'mustThrowResources' => $player != null
                && $this->service->isInPhase($player, MyrmesParameters::PHASE_WINTER)
                && $this->winterMYRService->mustDropResourcesForWinter($player)
        ]);
    }

    #[Route('/game/myrmes/{idGame}/displayPersonalBoard/{idPlayer}',
        name: 'app_game_myrmes_display_player_personal_board')]
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
                'isAnotherPlayerBoard' => true,
                'playerPhase' => $playerMYR->getPhase(),
                'actualSeason' => $this->service->getActualSeason($gameMYR),
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
                'mustThrowResources' => false
            ]);
    }

    #[Route('/game/myrmes/{id}/display/mainBoard/box/{tileId}/actions',
        name: 'app_game_myrmes_display_main_board_box_actions')]
    public function displayMainBoardBoxActions(
        #[MapEntity(id: 'id')] GameMYR $game,
        #[MapEntity(id: 'tileId')] TileMYR $tile): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $boardBox = $this->dataManagementMYRService->createBoardBox($game, $tile, 0, 0);
        $this->publishHighlightTile($game, $player, $tile);
        return $this->render('Game/Myrmes/MainBoard/displayBoardBoxActions.html.twig', [
            'game' => $game,
            'player' => $player,
            'selectedBox' => $boardBox,
            'needToPlay' => true, //$player == null ? false : $player->isTurnOfPlayer(),
            'playerPhase' => $player->getPhase(),
            'hasFinishedObligatoryHarvesting' => $this->harvestMYRService->areAllPheromonesHarvested($player),
            'canStillHarvest' => $this->harvestMYRService->canStillHarvest($player),
            'tile' => $tile,
        ]);

    }

    #[Route('/game/myrmes/{id}/display/personalBoard/throwResource/{playerResourceId}/actions',
        name: 'app_game_myrmes_display_throw_resource_actions')]
    public function displayThrowResourceActions(
        #[MapEntity(id: 'id')] GameMYR $game,
        #[MapEntity(id: 'playerResourceId')] PlayerResourceMYR $playerResourceMYR): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
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

    #[Route('/game/myrmes/{idGame}/lower/bonus/', name: 'app_game_myrmes_lower_bonus')]
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

    #[Route('/game/myrmes/{idGame}/confirm/bonus/', name: 'app_game_myrmes_confirm_bonus')]
    public function confirmBonus(
        #[MapEntity(id: 'idGame')] GameMYR $game,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $this->service->setPhase($player, MyrmesParameters::PHASE_BIRTH);
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
                    $this->service->getNursesAtPosition($player, MyrmesParameters::BASE_AREA)->first(),
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
        $this->service->setPhase($player, MyrmesParameters::PHASE_WORKER);
        $message = $player->getUsername() . " a confirmé le placement de ses nourrices";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("nurses placement confirmed", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/confirmNursesPlacement', name: 'app_game_myrmes_cancel_nurses')]
    public function cancelNursesPlacement(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        $this->birthMYRService->cancelNursesPlacement($player);

        $message = $player->getUsername() . " a annulé le placement de ses nourrices";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("nurses placement canceled", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/placeWorkerOnColonyLevelTrack/{level}', name: 'app_game_myrmes_place_worker_colony')]
    public function placeWorkerOnColonyLevelTrack(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        int $level
    ): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        $personalBoard = $player->getPersonalBoardMYR();
        try {
            //TODO: call service function to place worker on colony level track
        } catch (Exception) {
            $message = $player->getUsername()
                . " a essayé de placer une ouvrière sur le niveau de fourmilière "
                . $level
                . " mais n'a pas pu.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to place worker on colony', Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a placé une ouvrière sur le niveau de fourmilière " . $level;
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("placed worker on colony", Response::HTTP_OK);
    }

    #[Route('/game/muyrmes/{gameId}/moveAnt/{antId}/direction/{dir}', name:'app_game_myrmes_move_ant')]
    public function moveAnt(
        #[MapEntity(id: 'gameId')] GameMYR $gameMYR,
        #[MapEntity(id: 'antId')] GardenWorkerMYR $ant,
        int $direction

    ): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        if ($player !== $ant->getPlayer()) {
            return new Response('failed to move ant, not the correct player', Response::HTTP_FORBIDDEN);
        }
        try {
            $this->workerMYRService->workerMove($player, $ant, $direction);
        } catch (Exception) {
            $message = $player->getUsername()
                . " a essayé de déplacer la fourmi dans la direction "
                . $direction
                . " mais n'a pas pu.";
            $this->logService->sendPlayerLog($gameMYR, $player, $message);
            return new Response('failed to move ant in this direction', Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a déplacé la fourmi ". $ant->getId() . "dans la direction " . $direction;
        $this->logService->sendPlayerLog($gameMYR, $player, $message);
        return new Response("moved ant in this direction", Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/harvestResource/{tileId}', name: 'app_game_myrmes_harvest_resource')]
    public function harvestResource(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        #[MapEntity(id: 'tileId')] TileMYR $tileMYR
    ): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
            $this->harvestMYRService->harvestPheromone($player, $tileMYR);
        } catch (Exception $e) {
            $message = $player->getUsername()
                . " a essayé de récolter une ressource sur la tuile "
                . $tileMYR->getId()
                . " mais n'a pas pu.";
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
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        if(!$this->harvestMYRService->areAllPheromonesHarvested($player)) {
            $message = $player->getUsername()
                . " a essayé de mettre fin à la phase de récolte "
                . " mais n'a pas pu car la récolte obligatoire n'est pas finie.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to end harvest phase', Response::HTTP_FORBIDDEN);
        }
        if($this->workshopMYRService->canSetPhaseToWorkshop($player)) {
            $this->service->setPhase($player, MyrmesParameters::PHASE_WORKSHOP);
        } else {
            if($this->winterMYRService->canSetPhaseToWinter($game)) {
                $this->service->setPhase($player, MyrmesParameters::PHASE_WINTER);
            } else {
                $this->service->setPhase($player, MyrmesParameters::PHASE_EVENT);
            }
        }
        $message = $player->getUsername()
            . " a mis fin à la phase de récolte ";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('ended harvest phase', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/workshop/activate/anthillHolePlacement',
        name: 'app_game_myrmes_activate_anthill_hole_placement')]
    public function activateAnthillHolePlacementWorkshop(
        #[MapEntity(id: 'gameId')] GameMYR $gameMYR
    ): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        $boardBoxes = null;
        try {
            $boardBoxes = $this->dataManagementMYRService->organizeMainBoardRows($gameMYR);
        } catch (\Exception $e) {
            return new Response("Error while calculating main board tiles disposition",
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->render('/Game/Myrmes/index.html.twig', [
            'player' => $player,
            'game' => $gameMYR,
            'boardBoxes' => $boardBoxes,
            'isPreview' => true,
            'preys' => $gameMYR->getMainBoardMYR()->getPreys(),
            'isSpectator' => $player == null,
            'needToPlay' => true,//$player == null ? false : $player->isTurnOfPlayer(),
            'selectedBox' => null,
            'playerPhase' => $player== null ? $gameMYR->getPlayers()->first()->getPhase() : $player->getPhase(),
            'actualSeason' => $this->service->getActualSeason($gameMYR),
            'hasSelectedAnthillHolePlacement' => true,
            'possibleAnthillHolePlacement' => $this->workshopMYRService->getAvailableAnthillHolesPositions($player)
        ]);
    }

    #[Route('/game/myrmes/{gameId}/workshop/activate/anthillHolePlacement/{tileId}',
        name: 'app_game_myrmes_place_anthill_hole')]
    public function placeAnthillHoleWorkshop(
        #[MapEntity(id: 'gameId')] GameMYR $gameMYR,
        #[MapEntity(id: 'tileId')] TileMYR $tileMYR
    ): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
            $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tileMYR);
        } catch (Exception $e) {
            $message = $player->getUsername()
                . " a essayé de poser un trou de fourmilière "
                . " mais n'a pas pu.";
            $this->logService->sendPlayerLog($gameMYR, $player, $message);
            return new Response('failed to place anthill hole', Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername()
            . " a posé un trou de fourmilière "
            . " sur la tuile " . $tileMYR->getId();
        $this->logService->sendPlayerLog($gameMYR, $player, $message);
        if($this->service->canManageEndOfPhase($gameMYR, MyrmesParameters::PHASE_WORKSHOP)) {
            try {
                $this->workshopMYRService->manageEndOfWorkshop($gameMYR);
            } catch (Exception $e) {
                $message = "Le système a essayé de mettre fin à la phase atelier "
                    . " mais n'a pas pu.";
                $this->logService->sendSystemLog($gameMYR, $message);
                return new Response('failed to end workshop phase', Response::HTTP_FORBIDDEN);
            }
            $message = "Le système a mis fin à la phase atelier ";
            $this->logService->sendSystemLog($gameMYR, $message);
            return new Response('ended harvest phase', Response::HTTP_OK);
        }
        return new Response('placed an anthill hole', Response::HTTP_OK);
    }

    #[Route('/game/myrmes/{gameId}/throwResource/warehouse/{playerResourceId}',
        name: 'app_game_myrmes_throw_resource_warehouse')]
    public function throwResourceFromWarehouse(
        #[MapEntity(id: 'gameId')] GameMYR $game,
        #[MapEntity(id: 'playerResourceId')] PlayerResourceMYR $playerResourceMYR
    ): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
            $this->winterMYRService->removeCubeOfWarehouse($player, $playerResourceMYR);
        } catch (Exception $e) {
            $message = $player->getUsername()
                . " a essayé de jeter la ressource "
                . $playerResourceMYR->getId()
                . " de son entrepôt, mais n'a pas pu.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response('failed to throw this resource from warehouse', Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a jeté la ressource " . $playerResourceMYR->getId() . " de son entrepôt";
        $this->logService->sendPlayerLog($game, $player, $message);
        if($this->winterMYRService->canManageEndOfWinter($game)) {
            try {
                $this->winterMYRService->manageEndOfWinter($game);
            } catch (Exception $e) {
                $message =
                    "Le système a essayé de terminer la phase hiver "
                    . ", mais n'a pas pu.";
                $this->logService->sendSystemLog($game, $message);
                return new Response('failed to manage end of winter', Response::HTTP_FORBIDDEN);
            }
            $message = "Le système a mis fin à la phase hiver ";
            $this->logService->sendSystemLog($game, $message);
        }
        return new Response("threw resource from warehouse", Response::HTTP_OK);
    }

    #[Route('/Game/{gameId}/increaseAnthillLevel', name: 'app_game_myrmes_increase_anthill_level')]
    public function increaseAnthillLevel(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
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
        return new Response('increased anthill level', Response::HTTP_OK);
    }

    #[Route('/Game/{gameId}/createNurse', name: 'app_game_myrmes_create_nurse')]
    public function createNurse(
        #[MapEntity(id: 'gameId')] GameMYR $game
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
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
        return new Response("created new nurse");
    }

    #[Route('/Game/{gameId}/confirmWorkshopActions', name: 'app_game_myrmes_confirm_workshop_actions')]
    public function confirmWorkshopActions(
        #[MapEntity(id: 'gameId')] GameMYR $game
    )
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('invalid player', Response::HTTP_FORBIDDEN);
        }
        try {
            $player->setPhase(MyrmesParameters::PHASE_WINTER);
        } catch (Exception) {
            $message = $player->getUsername() . " a essayé de confirmer ses actions de l'atelier mais n'a pas pû.";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("failed to confirm workshop actions", Response::HTTP_FORBIDDEN);
        }
        $message = $player->getUsername() . " a confirmé ses actions de l'atelier.";
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response("confirmed workshop actions", Response::HTTP_OK);
    }

    /**
     * publishMainBoard: publish with mercure the main board
     * @param GameMYR $game
     * @return void
     */
    private function publishMainBoard(GameMYR $game, BoardBoxMYR $selectedBox) : void
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $response = $this->render('Game/Myrmes/MainBoard/mainBoard.html.twig',
            [
                'game' => $game,
                'player' => $player,
                'boardBoxes' =>  $this->dataManagementMYRService->organizeMainBoardRows($game),
                'isPreview' => true,
                'preys' => $game->getMainBoardMYR()->getPreys(),
                'isSpectator' => $player == null,
                'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
                'selectedBox' => $selectedBox,
            ]);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_glm',
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

    #[Route('/game/myrmes/{idGame}/displayPersonalObjectToPlace/{tileId}', name: 'app_game_myrmes_display_personal_object_to_place')]
    public function displayPersonalObjectToPlace(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR,
        #[MapEntity(id: 'tileId')] TileMYR $tileMYR
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }

        return $this->render('Game/Myrmes/MainBoard/displayObjectPlacement.html.twig', [
            'game' => $gameMYR,
            'player' => $player,
            'tile' => $tileMYR,
            'tiles' => $this->workerMYRService->getAvailablePheromones($player)
        ]);
    }

    #[Route('/game/myrmes/{idGame}/displayMainBoardTilePositionPossibilities/{tileId}/{tileType}/{orientation}',
        name: 'app_game_myrmes_display_mainBoard_tile_position_possibilities')]
    public function displayMainBoardTilePositionPossibilities(
        #[MapEntity(id: 'idGame')] GameMYR $gameMYR,
        #[MapEntity(id: 'tileId')] TileMYR $tileMYR,
        #[MapEntity(id: 'tileType')] int $tileType,
        #[MapEntity(id: 'orientation')] int $orientation,
    ) : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameMYR, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $tileTypeMYR = $this->service->getTileTypeFromTypeAndOrientation($tileType, $orientation);
        if ($tileTypeMYR == null) {
            return new Response('Invalid tile type', Response::HTTP_FORBIDDEN);
        }

        try {
            $positions = $this->workerMYRService->getAllAvailablePositions($player, $tileMYR, $tileTypeMYR);
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
}