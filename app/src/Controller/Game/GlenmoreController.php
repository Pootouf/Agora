<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\Glenmore\BoardBoxGLM;
use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Service\Game\Glenmore\DataManagementGLMService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use App\Service\Game\LogService;
use App\Service\Game\MessageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GlenmoreController extends AbstractController
{

    public function __construct(private GLMService $service,
                                private MessageService $messageService,
                                private DataManagementGLMService $dataManagementGLMService,
                                private TileGLMService $tileGLMService,
                                private LogService $logService)
    {}

    #[Route('/game/glenmore/{id}', name: 'app_game_show_glm')]
    public function showGame(GameGLM $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $isSpectator = false;
        $needToPlay = false;
        if ($player == null) {
            $player = $game->getPlayers()->get(0);
            $isSpectator = true;
        } else {
            //$needToPlay = $player->isTurnOfPlayer();
        }
        $messages = $this->messageService->receiveMessage($game->getId());
        return $this->render('/Game/Glenmore/index.html.twig', [
            'game' => $game,
            'player' => $player,
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            'isGameFinished' => $this->service->isGameEnded($game),
            'selectedTile' => null,
            'adjacentTiles' => null,
            'potentialNeighbours' => null,
            'currentDrawTile' => $this->tileGLMService->getActiveDrawTile($game),
            'personalBoardTiles' => $this->dataManagementGLMService->organizePersonalBoardRows($player),
            'boardTiles' => $this->dataManagementGLMService->organizeMainBoardRows(
                $this->dataManagementGLMService->createBoardBoxes($game)),
            'whiskyCount' => $this->dataManagementGLMService->getWhiskyCount($player),
            'messages' => $messages,
        ]);
    }

    #[Route('game/glenmore/{idGame}/display/propertyCards', name: 'app_game_glenmore_display_player_property_cards')]
    public function displayPropertyCards(
        #[MapEntity(id: 'idGame')] GameGLM $gameGLM): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameGLM, $this->getUser()->getUsername());
        return $this->render('Game/Glenmore/PersonalBoard/displayPropertyCards.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('game/glenmore/{idGame}/select/tile/mainBoard/{idTile}', name: 'app_game_glenmore_select_tile_on_mainboard')]
    public function selectTileOnMainBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] BoardTileGLM $tile
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        if ($this->service->getActivePlayer($game) !== $player) {
            return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
        }
        try {
            $this->tileGLMService->assignTileToPlayer($tile, $player);
        } catch (Exception) {
            $message = $player->getUsername() . " tried to choose tile " . $tile->getTile()->getId()
                . " but could not afford it";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("can't afford this tile", Response::HTTP_FORBIDDEN);
        }
        // TODO Publish management
        $message = $player->getUsername() . " chose tile " . $tile->getTile()->getId();
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('player selected this tile', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/tile/personalBoard/{idTile}', name: 'app_game_glenmore_select_tile_on_personalboard')]
    public function selectTileOnPersonalBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        return $this->render('/Game/Glenmore/PersonalBoard/selectTile.html.twig', [
            'selectedTile' => $tile,
            'game' => $game,
            'player' => $player,
        ]);
    }

    /**
     * manageEndOfRound : at the end of a player's round, replace the good number of tiles, proceeds
     *  to count points if needed. Finally, ends the game if the game must end
     * @param GameGLM $gameGLM
     * @return void
     */
    private function manageEndOfRound(GameGLM $gameGLM) : void
    {
        $activePlayer = $this->service->getActivePlayer($gameGLM);
        $mainBoard = $gameGLM->getMainBoard();
        $this->service->endRoundOfPlayer($gameGLM, $activePlayer, $mainBoard->getLastPosition());
        $newPlayer = $this->service->getActivePlayer($gameGLM);
        $amountOfTilesToReplace = $this->tileGLMService->getAmountOfTileToReplace($mainBoard);
        $drawTiles = $this->tileGLMService->getActiveDrawTile($gameGLM);
        $oldLevel = $drawTiles->getLevel();
        $newLevel = $oldLevel;
        for ($i = 0; $i < $amountOfTilesToReplace; ++$i) {
            $this->tileGLMService->placeNewTile($newPlayer, $drawTiles);
            $drawTiles = $this->tileGLMService->getActiveDrawTile($gameGLM);
            if ($drawTiles == null) {
                break;
            }
            $newLevel = $drawTiles->getLevel();
        }
        if ($newLevel > $oldLevel) {
            $this->service->manageEndOfRound($gameGLM, $newLevel);
        }
        if ($this->service->isGameEnded($gameGLM)) {
            // TODO PUBLISH WINNER(S)
            $winners = $this->service->getWinner($gameGLM);
            $message = "";
            foreach ($winners as $winner) {
                $message .=  $winner->getUsername() . " ";
            }
            $message .= " won the game " . $gameGLM->getId();
            $this->logService->sendSystemLog($gameGLM, $message);
        } else {
            // TODO SOME PUBLISH
        }
    }

}