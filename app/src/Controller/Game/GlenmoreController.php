<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\Glenmore\BoardBoxGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Service\Game\Glenmore\DataManagementGLMService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use App\Service\Game\MessageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
                                private TileGLMService $tileGLMService)
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

    /*#[Route('game/glenmore/{idGame}/personal/board', name: 'app_game_glenmore_display_player_personal_board')]
    public function displayPersonalBoard(
        #[MapEntity(id: 'idGame')] GameGLM $gameGLM): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameGLM, $this->getUser()->getUsername());
        return $this->render('Game/Glenmore/PersonalBoard/personalBoard.html.twig', [
            'player' => $player->getPersonalBoard(),
        ]);
    }*/


}