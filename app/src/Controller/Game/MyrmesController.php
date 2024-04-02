<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\Myrmes\BoardBoxMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Service\Game\LogService;
use App\Service\Game\Myrmes\BirthMYRService;
use App\Service\Game\Myrmes\EventMYRService;
use App\Service\Game\Myrmes\DataManagementMYRService;
use App\Service\Game\Myrmes\MYRService;
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
            'needToPlay' => true,//$player == null ? false : $player->isTurnOfPlayer(),
            'selectedBox' => null,
            'playerPhase' => $player->getPhase(),
            'isAnotherPlayerBoard' => false,
            'isBirthPhase' => $this->service->isInPhase($player, MyrmesParameters::PHASE_BIRTH),
            'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::LARVAE_AREA)->count(),
            'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::SOLDIERS_AREA)->count(),
            'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::WORKER_AREA)->count()
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
            'nursesOnLarvaeBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::LARVAE_AREA)->count(),
            'nursesOnSoldiersBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::SOLDIERS_AREA)->count(),
            'nursesOnWorkersBirthTrack' => $this->service->getNursesAtPosition($player, MyrmesParameters::WORKER_AREA)->count()
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
                'isAnotherPlayerBoard' => true,
                'isBirthPhase' => $this->service->isInPhase($playerMYR, MyrmesParameters::PHASE_BIRTH),
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
            'playerPhase' => $player->getPhase()
        ]);

    }

    #[Route('/game/myrmes/{gameId}/up/bonus/', name: 'app_game_myrmes_up_bonus')]
    public function upBonus(
        #[MapEntity(id: 'gameId')] GameMYR $game,
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
        #[MapEntity(id: 'gameId')] GameMYR $game,
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
        #[MapEntity(id: 'gameId')] GameMYR $game,
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
    )
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
}