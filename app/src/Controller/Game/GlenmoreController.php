<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\GameParameters;
use App\Entity\Game\DTO\GameTranslation;
use App\Entity\Game\DTO\Player;
use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\GlenmoreTranslation;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Service\Game\Glenmore\CardGLMService;
use App\Service\Game\Glenmore\DataManagementGLMService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use App\Service\Game\Glenmore\WarehouseGLMService;
use App\Service\Game\LogService;
use App\Service\Game\MessageService;
use App\Service\Game\PublishService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
                                private CardGLMService $cardGLMService,
                                private LogService $logService,
                                private ResourceGLMRepository $resourceGLMRepository,
                                private PublishService $publishService,
                                private WarehouseGLMService $warehouseGLMService,
                                private EntityManagerInterface $entityManager)
    {}

    #[Route('/game/glenmore/{id}', name: 'app_game_show_glm')]
    public function showGame(GameGLM $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN);
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $isSpectator = false;
        $needToPlay = false;
        if ($player == null) {
            $player = $game->getPlayers()->get(0);
            $isSpectator = true;
        } else {
            $needToPlay = $player->isTurnOfPlayer();
        }
        $messages = $this->messageService->receiveMessage($game->getId());
        $activePlayer = $this->service->getActivePlayer($game);
        $personalBoard = $activePlayer->getPersonalBoard();
        $movementPhase = $this->service->isInMovementPhase($activePlayer);
        $activationPhase = $this->service->isInActivationPhase($activePlayer);
        $buyingPhase = $this->service->isInBuyingPhase($activePlayer);
        $sellingPhase = $this->service->isInSellingPhase($activePlayer);
        return $this->render('/Game/Glenmore/index.html.twig', [
            'game' => $game,
            'player' => $player,
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            'isGameFinished' => $this->service->isGameEnded($game),
            'selectedTile' => null,
            'activableTiles' => $this->service->isInActivationPhase($player) ?
                $this->tileGLMService->getActivableTiles($personalBoard->getPlayerTiles()->last())
                : null,
            'potentialNeighbours' => null,
            'currentDrawTile' => $this->tileGLMService->getActiveDrawTile($game),
            'personalBoardTiles' => $this->dataManagementGLMService->organizePersonalBoardRows(
                $player,
                $player->getPersonalBoard()->getBuyingTile() == null ? [] :
                $this->tileGLMService->verifyAllPositionOnPersonalBoard(
                    $player->getPersonalBoard(),
                    $player->getPersonalBoard()->getBuyingTile()->getBoardTile()->getTile()
                )),
            'boardTiles' => $this->dataManagementGLMService->organizeMainBoardRows(
                $this->dataManagementGLMService->createBoardBoxes($game)),
            'activatedBuyingPhase' => $buyingPhase,
            'activatedActivationPhase' => $activationPhase,
            'activatedSellingPhase' => $sellingPhase,
            'activatedMovementPhase' => $movementPhase,
            'activatedResourceSelection' => $player->isActivatedResourceSelection(),
            'selectedResources' => $player->getPersonalBoard()->getSelectedResources(),
            'activatedNewResourceAcquisition' => $player->isActivatedNewResourcesAcqusition(),
            'chosenNewResources' => $player->getPersonalBoard()->getCreatedResources(),
            'selectedWarehouseProduction' => null,
            'isWarehouseMoneySelected' => false,
            'playersDataResources' => $this->dataManagementGLMService->getPlayersResourcesData($game),
            'buyingTile' => $player->getPersonalBoard()->getBuyingTile(),
            'messages' => $messages,
        ]);
    }

    #[Route('game/glenmore/{idGame}/display/propertyCards/{idPlayer}',
            name: 'app_game_glenmore_display_player_property_cards')]
    public function displayPropertyCards(
        #[MapEntity(id: 'idGame')] GameGLM $gameGLM,
        #[MapEntity(id: 'idPlayer')] PlayerGLM $playerGLM): Response
    {
        if ($gameGLM->isPaused() || !$gameGLM->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($gameGLM, $playerGLM->getUsername());
        return $this->render('Game/Glenmore/PersonalBoard/displayPropertyCards.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('game/glenmore/{idGame}/select/money/warehouse/production/mainBoard/{idResourceLine}',
        name: 'app_game_glenmore_select_money_warehouse_production_on_mainboard')]
    public function selectMoneyWarehouseProductionOnMainBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idResourceLine')] WarehouseLineGLM $line
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        return $this->render('Game/Glenmore/MainBoard/Warehouse/warehouseActions.html.twig', [
            'player' => $player,
            'game' => $game,
            'selectedWarehouseProduction' => $line,
            'isMoneyWarehouseSelected' => true
        ]);
    }

    #[Route('game/glenmore/{idGame}/select/resource/warehouse/production/mainBoard/{idResourceLine}',
        name: 'app_game_glenmore_select_resource_warehouse_production_on_mainboard')]
    public function selectResourceWarehouseProductionOnMainBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idResourceLine')] WarehouseLineGLM $line
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        return $this->render('Game/Glenmore/MainBoard/Warehouse/warehouseActions.html.twig', [
            'player' => $player,
            'game' => $game,
            'selectedWarehouseProduction' => $line,
            'isMoneyWarehouseSelected' => false
        ]);
    }

    #[Route('game/glenmore/{idGame}/buy/resource/warehouse/production/mainBoard/{idResourceLine}',
        name: 'app_game_glenmore_buy_resource_warehouse_production_on_mainboard')]
    public function buyResourceWarehouseProductionOnMainBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idResourceLine')] WarehouseLineGLM $line
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->service->getActivePlayer($game) !== $player) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_BUY_RESOURCE_NOT_PLAYER_ROUND);
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        try {
            $this->warehouseGLMService->buyResourceFromWarehouse($player, $line->getResource());
        } catch (Exception) {
            $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                GlenmoreTranslation::WARNING,
                GlenmoreTranslation::CANNOT_BUY_RESOURCE, GameParameters::ALERT_NOTIFICATION_TYPE,
                GameParameters::NOTIFICATION_COLOR_RED, $player->getUsername());
            $message = $player->getUsername()
                . GlenmoreTranslation::TRY_BUY_RESOURCE
                . $line->getResource()->getColor()
                . GlenmoreTranslation::NOT_ABLE;
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response(GlenmoreTranslation::RESPONSE_CANNOT_AFFORD_RESOURCE, Response::HTTP_FORBIDDEN);
        }
        $prod = $this->typeResources($line->getResource()->getColor());
        $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
            GlenmoreTranslation::VALIDATE_BUY,
            GlenmoreTranslation::BUY_DESCRIPTION.$prod,
            GameParameters::VALIDATION_NOTIFICATION_TYPE,
            GameParameters::NOTIFICATION_COLOR_GREEN,
            $player->getUsername());
        $this->publishMainBoardPreview($game);
        $this->publishRanking($game);
        $this->publishMainBoard($game);
        $message = $player->getUsername()
            . GlenmoreTranslation::BUY_RESOURCE
            . $line->getResource()->getColor();
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response(GlenmoreTranslation::RESPONSE_RESOURCE_BOUGHT, Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/activate/selling/resource/warehouse/production/mainBoard/{idResourceLine}',
        name: 'app_game_glenmore_activate_selling_resource_warehouse_production_on_mainboard')]
    public function activateSellingResourceWarehouseProductionOnMainBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idResourceLine')] WarehouseLineGLM $line
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->service->getActivePlayer($game) !== $player) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_SELL_RESOURCE_NOT_PLAYER_ROUND);
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if ($player->isActivatedResourceSelection()) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_SELL_RESOURCE_ALREADY_IN_SELECTION);
            return new Response(GlenmoreTranslation::RESPONSE_TRY_SELL_RESOURCE_ALREADY_IN_SELECTION,
                Response::HTTP_FORBIDDEN);
        }
        $player->getPersonalBoard()->setResourceToSell($line->getResource());
        $this->service->setPreviousPhase($player, $player->getRoundPhase());
        $this->service->setPhase($player, GlenmoreParameters::SELLING_PHASE);
        $player->setActivatedResourceSelection(true);
        $this->entityManager->persist($player);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::ENTER_SELLING_PHASE);
        return new Response(GlenmoreTranslation::RESPONSE_ENTER_SELLING_PHASE, Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/tile/mainBoard/{idTile}',
            name: 'app_game_glenmore_select_tile_on_mainboard')]
    public function selectTileOnMainBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] BoardTileGLM $tile
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->service->getActivePlayer($game) !== $player) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_SELECT_TILE_NOT_PLAYER_ROUND);
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        try {
            $possiblePlacement = $this->tileGLMService->assignTileToPlayer($tile, $player);
        } catch (Exception $e) {
            $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                GlenmoreTranslation::WARNING,
                GlenmoreTranslation::CANNOT_BUY_OR_PLACE_TILE,
                GameParameters::ALERT_NOTIFICATION_TYPE,
                GameParameters::NOTIFICATION_COLOR_RED,
                $player->getUsername());
            $message = $player->getUsername()
                . GlenmoreTranslation::CHOOSE_TILE . $tile->getId()
                . GlenmoreTranslation::CANNOT_BUY;
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response(GlenmoreTranslation::RESPONSE_CANNOT_AFFORD_TILE
                . $e->getMessage(), Response::HTTP_FORBIDDEN);
        }
        if($this->tileGLMService->hasBuyCost($tile)) {
            $player->setActivatedResourceSelection(true);
            $player->setRoundPhase(GlenmoreParameters::BUYING_PHASE);
        } else {
            $player->setRoundPhase(GlenmoreParameters::ACTIVATION_PHASE);
            $player->setActivatedResourceSelection(false);
        }
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $this->publishMainBoardPreview($game);
        $this->publishRanking($game);
        $this->publishMainBoard($game);
        $this->publishPersonalBoard($player, $possiblePlacement);
        $this->publishPersonalBoardSpectator($game);
        $this->publishPlayerRoundManagement($game);
        $message = $player->getUsername() . GlenmoreTranslation::CHOOSE_TILE . $tile->getId();
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response(GlenmoreTranslation::RESPONSE_SELECTED_TILE, Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/tile/mainBoard/{coordX}/{coordY}',
        name: 'app_game_glenmore_put_tile_on_personal_board')]
    public function putTileOnPersonalBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        int $coordX,
        int $coordY
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->service->getActivePlayer($game) !== $player) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_PLACE_TILE_NOT_PLAYER_ROUND);
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if($player->isActivatedResourceSelection()) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_PLACE_TILE_IN_RESOURCE_SELECTION);
            return new Response(GlenmoreTranslation::RESPONSE_TRY_PLACE_TILE_IN_RESOURCE_SELECTION,
                Response::HTTP_FORBIDDEN);
        }
        try {
            $this->tileGLMService->setPlaceTileAlreadySelected($player, $coordX, $coordY);
        } catch (Exception $e) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::NOT_ABLE_TO_PLACE_TILE .
                $player->getPersonalBoard()->getBuyingTile()->getBoardTile()->getTile()->getId());
            return new Response(GlenmoreTranslation::RESPONSE_NOT_ABLE_TO_PLACE_TILE . $e,
                Response::HTTP_FORBIDDEN);
        }
        $this->service->setPhase($player, GlenmoreParameters::ACTIVATION_PHASE);
        $player->setActivatedResourceSelection(false);
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->last();

        if ($this->tileGLMService->giveBuyBonus($playerTile) == -1) {
            $player->setActivatedNewResourcesAcqusition(true);
            $this->publishCreateResource($playerTile);
        } else {
            $this->publishPersonalBoard($player, []);
            $this->publishPersonalBoardSpectator($game);
        }
        $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
            GlenmoreTranslation::VALIDATE_ACTION,
            GlenmoreTranslation::TILE_PLACED_DESCRIPTION,
            GameParameters::VALIDATION_NOTIFICATION_TYPE,
            GameParameters::NOTIFICATION_COLOR_GREEN,
            $player->getUsername());
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $this->publishPlayerRoundManagement($game);
        $this->publishRanking($game);
        $this->publishMainBoardPreview($game);
        $message = $player->getUsername() . GlenmoreTranslation::TILE_PLACED
            . $playerTile->getTile()->getId();
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response(GlenmoreTranslation::RESPONSE_TILE_PLACED, Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/tile/personalBoard/{idTile}',
            name: 'app_game_glenmore_select_tile_on_personalboard')]
    public function selectTileOnPersonalBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        return $this->render('/Game/Glenmore/PersonalBoard/selectTile.html.twig', [
            'selectedTile' => $tile,
            'game' => $game,
            'player' => $player,
            'activableTiles' => $this->tileGLMService
                ->getActivableTiles($player->getPersonalBoard()->getPlayerTiles()->last()),
            'activatedBuyingPhase' => $this->service->isInBuyingPhase($player),
            'activatedActivationPhase' => $this->service->isInActivationPhase($player),
            'activatedSellingPhase' => $this->service->isInSellingPhase($player),
            'selectedResources' => $player->getPersonalBoard()->getSelectedResources(),
            'activatedResourceSelection' => $player->isActivatedResourceSelection(),
            'activatedNewResourceAcquisition' => $player->isActivatedNewResourcesAcqusition(),
            'chosenNewResources' => $player->getPersonalBoard()->getCreatedResources(),
            'activatedMovementPhase' => $this->service->isInMovementPhase($player),
            'buyingTile' => $player->getPersonalBoard()->getBuyingTile()
        ]);
    }

    #[Route('game/glenmore/{idGame}/select/{idTile}/resource/{idPlayerTileResource}',
            name: 'app_game_glenmore_select_resource')]
    public function selectResource(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idPlayerTileResource')] PlayerTileResourceGLM $resourceGLM,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $phase = $player->getRoundPhase();
        if ($phase == GlenmoreParameters::BUYING_PHASE) {
            try {
                $this->tileGLMService->selectResourcesFromTileToBuy($tile, $resourceGLM->getResource());
            } catch (Exception $e) {
                $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                    GlenmoreTranslation::WARNING,
                    GlenmoreTranslation::WRONG_RESOURCE_SELECTED_TO_BUY,
                    GameParameters::ALERT_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_RED,
                    $player->getUsername());
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername() . GlenmoreTranslation::TRY_SELECT_RESOURCE_NOT_ABLE);
                return new Response($e->getMessage(), Response::HTTP_FORBIDDEN);
            }
        } elseif ($phase == GlenmoreParameters::ACTIVATION_PHASE) {
            try {
                $this->tileGLMService->selectResourcesFromTileToActivate($tile, $resourceGLM->getResource());
            } catch (Exception $e) {
                $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                    GlenmoreTranslation::WARNING,
                    GlenmoreTranslation::WRONG_RESOURCE_SELECTED_TO_ACTIVATE,
                    GameParameters::ALERT_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_RED,
                    $player->getUsername());
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername() . GlenmoreTranslation::TRY_SELECT_RESOURCE_NOT_ABLE);
                return new Response($e->getMessage(), Response::HTTP_FORBIDDEN);
            }
        } elseif ($phase == GlenmoreParameters::SELLING_PHASE) {
            try {
                $this->tileGLMService->selectResourcesFromTileToSellResource($tile, $resourceGLM->getResource());
            } catch (Exception $e) {
                $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                    GlenmoreTranslation::WARNING,
                    GlenmoreTranslation::WRONG_RESOURCE_SELECTED_TO_SELL,
                    GameParameters::ALERT_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_RED,
                    $player->getUsername());
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername() . GlenmoreTranslation::TRY_SELECT_RESOURCE_NOT_ABLE);
                return new Response($e->getMessage(), Response::HTTP_FORBIDDEN);
            }
        }
        $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
            GlenmoreTranslation::VALIDATE_RESOURCE_SELECTION,
            GlenmoreTranslation::RESOURCE_SELECTION_DESCRIPTION,
            GameParameters::VALIDATION_NOTIFICATION_TYPE,
            GameParameters::NOTIFICATION_COLOR_GREEN,
            $player->getUsername());
        $this->publishSelectResource($tile);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername()
            . GlenmoreTranslation::RESOURCE_SELECTED
            . $resourceGLM->getResource()->getId());
        return new Response(GlenmoreTranslation::RESPONSE_RESOURCE_SELECTED, Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/leader',
        name: 'app_game_glenmore_select_leader')]
    public function selectLeader(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $phase = $player->getRoundPhase();
        if ($phase == GlenmoreParameters::BUYING_PHASE) {
            try {
                $this->tileGLMService->selectLeader($player);
            } catch (Exception $e) {
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername() . GlenmoreTranslation::TRY_SELECT_LEADER_NOT_ABLE);
                return new Response($e->getMessage(), Response::HTTP_FORBIDDEN);
            }
        }
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::LEADER_SELECTED);
        return new Response(GlenmoreTranslation::RESPONSE_LEADER_SELECTED, Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/{idTile}/resource/acquisition/{resource}',
            name: 'app_game_glenmore_select_new_resource_acquisition')]
    public function selectNewResourceAcquisition(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile,
        string $resource
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $production_resource = $this->resourceGLMRepository->findOneBy(
            [
                "type" => GlenmoreParameters::PRODUCTION_RESOURCE,
                "color" => $resource
            ]);
        if($tile->getTile()->getName() === GlenmoreParameters::CARD_LOCH_LOCHY) {
            try {
                $this->cardGLMService->selectResourceForLochLochy($player, $production_resource);
            } catch (Exception) {
                $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                    GlenmoreTranslation::WARNING,
                    GlenmoreTranslation::CANNOT_SELECT_MORE_RESOURCES,
                    GameParameters::ALERT_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_RED,
                    $player->getUsername());
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername() . GlenmoreTranslation::TRY_SELECT_RESOURCE_NOT_ABLE);
                return new Response(GlenmoreTranslation::RESPONSE_CANNOT_SELECT_MORE_RESOURCE,
                    Response::HTTP_FORBIDDEN);
            }
        } elseif ($tile->getTile()->getName() === GlenmoreParameters::CARD_IONA_ABBEY) {
            try {
                $this->tileGLMService->selectResourceForIonaAbbey($player, $production_resource);
            } catch (Exception) {
                $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                    GlenmoreTranslation::WARNING,
                    GlenmoreTranslation::CANNOT_SELECT_MORE_RESOURCES,
                    GameParameters::ALERT_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_RED,
                    $player->getUsername());
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername() . GlenmoreTranslation::TRY_SELECT_RESOURCE_NOT_ABLE);
                return new Response(GlenmoreTranslation::RESPONSE_CANNOT_SELECT_MORE_RESOURCE,
                    Response::HTTP_FORBIDDEN);
            }
            $this->publishPersonalBoardSpectator($game);
        }
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::RESOURCE_SELECTED . $resource);
        return new Response(GlenmoreTranslation::RESPONSE_RESOURCE_SELECTED ,Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/remove/{idTile}/villager/{idPlayerTileResource}',
            name: 'app_game_glenmore_remove_villager')]
    public function removeVillager(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idPlayerTileResource')] PlayerTileResourceGLM $resourceGLM,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->service->getActivePlayer($game) !== $player) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername()
                . GlenmoreTranslation::TRY_GET_OUT_VILLAGER_NOT_PLAYER_ROUND);
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        try {
            $this->tileGLMService->removeVillager($tile);
        } catch (Exception $e) {
            $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                GlenmoreTranslation::WARNING,
                GlenmoreTranslation::CANNOT_GET_OUT_LAST_VILLAGER,
                GameParameters::ALERT_NOTIFICATION_TYPE,
                GameParameters::NOTIFICATION_COLOR_RED,
                $player->getUsername());
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_GET_OUT_VILLAGER_NOT_ABLE);
            return new Response(GlenmoreTranslation::RESPONSE_INVALID_MOVE . $e->getMessage(),
                Response::HTTP_FORBIDDEN);
        }
        $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
            GlenmoreTranslation::VALIDATE_LEADER,
            GlenmoreTranslation::LEADER_DESCRIPTION,
            GameParameters::VALIDATION_NOTIFICATION_TYPE,
            GameParameters::NOTIFICATION_COLOR_GREEN,
            $player->getUsername());
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishRanking($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::GET_OUT_VILLAGER);

        return new Response(GlenmoreTranslation::RESPONSE_VILLAGER_REMOVED, Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/activate/{idTile}', name: 'app_game_glenmore_activate_tile')]
    public function activateTile(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $this->tileGLMService->chooseTileToActivate($tile);
        if ($this->service->getActivePlayer($game) !== $player) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_ACTIVATE_TILE_NOT_PLAYER_ROUND);
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if(!$this->tileGLMService->hasActivationCost($tile)) {
            $player->setActivatedResourceSelection(false);
            try {
                $activableTiles = $this->tileGLMService
                    ->getActivableTiles($player->getPersonalBoard()->getPlayerTiles()->last());
                $this->tileGLMService->activateBonus($tile, $player, $activableTiles);
            } catch (Exception $e) {
                $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                    GlenmoreTranslation::WARNING,
                    GlenmoreTranslation::CANNOT_ACTIVATE_TILE,
                    GameParameters::ALERT_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_RED,
                    $player->getUsername());
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername() . GlenmoreTranslation::TRY_ACTIVATE_TILE
                    . $tile->getTile()->getId()
                    . GlenmoreTranslation::NOT_ABLE);
                return new Response(GlenmoreTranslation::RESPONSE_CANNOT_ACTIVATE_TILE
                    . $e->getMessage(),
                    Response::HTTP_FORBIDDEN);
            }
        } else {
            $this->service->setPhase($player, GlenmoreParameters::ACTIVATION_PHASE);
            $player->setActivatedResourceSelection(true);
        }
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishRanking($game);
        if($tile->getTile()->getName() == GlenmoreParameters::CARD_IONA_ABBEY) {
            $this->publishCreateResource($tile);
        }
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::ACTIVATE_TILE
            . $tile->getTile()->getId());
        return new Response(GlenmoreTranslation::RESPONSE_TILE_ACTIVATED,
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/validate/activation', name: 'app_game_glenmore_validate_activation_tile')]
    public function validateActivationTile(
        #[MapEntity(id: 'idGame')] GameGLM $game,
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $tile = $player->getPersonalBoard()->getActivatedTile();
        try {
            $activableTiles = $this->tileGLMService
                ->getActivableTiles($player->getPersonalBoard()->getPlayerTiles()->last());
            $this->tileGLMService->activateBonus($tile, $player, $activableTiles);
        } catch (Exception) {
            $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                GlenmoreTranslation::WARNING,
                GlenmoreTranslation::CANNOT_ACTIVATE_TILE,
                GameParameters::ALERT_NOTIFICATION_TYPE,
                GameParameters::NOTIFICATION_COLOR_RED,
                $player->getUsername());
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_ACTIVATE_TILE
                . $tile->getTile()->getId()
                . GlenmoreTranslation::NOT_ABLE);
            return new Response(GlenmoreTranslation::RESPONSE_CANNOT_ACTIVATE_TILE,
                Response::HTTP_FORBIDDEN);
        }
        $player->setActivatedResourceSelection(false);
        $this->service->setPhase($player, GlenmoreParameters::ACTIVATION_PHASE);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishRanking($game);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::ACTIVATE_TILE
            . $tile->getTile()->getId());
        return new Response(GlenmoreTranslation::RESPONSE_TILE_ACTIVATED,
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/end/activation', name: 'app_game_glenmore_end_activate_tile')]
    public function endTileActivation(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $this->service->setPhase($player, GlenmoreParameters::MOVEMENT_PHASE);
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishRanking($game);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::FINISH_ACTIVATION_PHASE);
        return new Response(
            $player->getUsername().GlenmoreTranslation::RESPONSE_FINISH_ACTIVATION_PHASE,
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/move/{idTile}/villager/direction/{dir}',
            name: 'app_game_glenmore_move_villager')]
    public function moveVillager(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile,
        int $dir
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->service->getActivePlayer($game) !== $player) {
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_MOVE_VILLAGER_NOT_PLAYER_ROUND);
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        try {
            $targetedTile = $this->tileGLMService->moveVillager($tile, $dir);
        } catch (Exception $e) {
            $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                GlenmoreTranslation::WARNING,
                GlenmoreTranslation::CANNOT_MOVE_HERE,
                GameParameters::ALERT_NOTIFICATION_TYPE,
                GameParameters::NOTIFICATION_COLOR_RED,
                $player->getUsername());
            $this->logService->sendPlayerLog($game, $player,
                $player->getUsername() . GlenmoreTranslation::TRY_MOVE_VILLAGER
                . $tile->getTile()->getId() . GlenmoreTranslation::IN_DIRECTION
                . $dir . GlenmoreTranslation::NOT_ABLE);
            return new Response(GlenmoreTranslation::RESPONSE_CANNOT_MOVE_VILLAGER
                . $e->getMessage(), Response::HTTP_FORBIDDEN);
        }
        $this->publishMoveVillagerOnPersonalBoard($game, $player, $tile, $targetedTile);
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::MOVE_VILLAGER
            . $tile->getTile()->getId() . GlenmoreTranslation::IN_DIRECTION . $dir);
        return new Response(GlenmoreTranslation::RESPONSE_MOVE_VILLAGER,
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/validate/{idTile}/resource/acquisition',
            name: 'app_game_glenmore_validate_new_resources_acquisition')]
    public function validateNewResourcesAcquisition(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if($tile->getTile()->getName() === GlenmoreParameters::CARD_LOCH_LOCHY) {
            $this->cardGLMService->validateTakingOfResourcesForLochLochy($player);
        } elseif ($tile->getTile()->getName() === GlenmoreParameters::CARD_IONA_ABBEY) {
            try {
                $this->tileGLMService->validateTakingOfResourcesForIonaAbbey($player);
            } catch (Exception $e) {
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername() . GlenmoreTranslation::TRY_ACTIVATE_TILE
                    . $tile->getTile()->getName());
                return new Response(GlenmoreTranslation::RESPONSE_CANNOT_ACTIVATE_TILE
                    . $e->getMessage(), Response::HTTP_FORBIDDEN);
            }
        }
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishRanking($game);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::FINISH_ACQUISITION_PHASE);
        return new Response($player->getUsername()
            .GlenmoreTranslation::RESPONSE_FINISH_ACQUISITION_PHASE,
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/cancel/{idTile}/resource/acquisition',
            name: 'app_game_glenmore_cancel_new_resources_acquisition')]
    public function cancelNewResourcesAcquisition(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $this->cardGLMService->clearCreatedResources($player);
        $this->publishCreateResource($tile);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::CANCEL_SELECTION);
        return new Response(GlenmoreTranslation::RESPONSE_CANCEL_SELECTION,
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/validate/resources/selection',
            name: 'app_game_glenmore_validate_resources_selection')]
    public function validateResourcesSelection(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $playerPhase = $player->getRoundPhase();
        if ($playerPhase == GlenmoreParameters::BUYING_PHASE) {
            if(!$this->tileGLMService->canBuyTileWithSelectedResources(
                $player,
                $player->getPersonalBoard()->getBuyingTile()->getBoardTile()->getTile()
            )) {
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername()
                    . GlenmoreTranslation::NOT_SELECTED_NEEDED_RESOURCES);
                return new Response(GlenmoreTranslation::RESPONSE_NOT_SELECTED_NEEDED_RESOURCES,
                    Response::HTTP_FORBIDDEN);
            }

        } elseif ($playerPhase == GlenmoreParameters::ACTIVATION_PHASE) {
            try {
                $activableTiles = $this->tileGLMService
                    ->getActivableTiles($player->getPersonalBoard()->getPlayerTiles()->last());
                $this->tileGLMService->activateBonus(
                    $player->getPersonalBoard()->getActivatedTile(),
                    $player,
                    $activableTiles
                );
            } catch (Exception $e) {
                $this->logService->sendPlayerLog($game, $player,
                    $player->getUsername()
                    . GlenmoreTranslation::NOT_SELECTED_NEEDED_RESOURCES);
                return new Response($e->getMessage() .
                    GlenmoreTranslation::RESPONSE_NOT_SELECTED_NEEDED_RESOURCES,
                    Response::HTTP_FORBIDDEN);
            }
            $this->service->setPhase($player, GlenmoreParameters::MOVEMENT_PHASE);

        } elseif ($playerPhase == GlenmoreParameters::SELLING_PHASE) {
            try {
                $this->warehouseGLMService->sellResource(
                    $player,
                    $player->getPersonalBoard()->getResourceToSell(),
                    $player->getPersonalBoard()->getSelectedResources()->first()
                );
                $prod = $this->typeResources($player->getPersonalBoard()->getResourceToSell()->getColor());
                $this->publishNotification($game, GlenmoreParameters::NOTIFICATION_DURATION,
                    GlenmoreTranslation::VALIDATE_SELLING,
                    GlenmoreTranslation::SELL_DESCRIPTION . $prod,
                    GameParameters::VALIDATION_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_GREEN,
                    $player->getUsername());
            } catch (Exception) {
                $message = $player->getUsername() . GlenmoreTranslation::TRY_SELL_RESOURCE
                    . $player->getPersonalBoard()->getResourceToSell()->getId()
                    . GlenmoreTranslation::NOT_ABLE;
                $this->logService->sendPlayerLog($game, $player, $message);
                return new Response(GlenmoreTranslation::RESPONSE_CANNOT_SELL_RESOURCE,
                    Response::HTTP_FORBIDDEN);
            }
            $this->service->setPhase($player, $player->getPreviousPhase());
            $this->service->setPreviousPhase($player, null);
            $this->publishMainBoardPreview($game);
            $this->publishMainBoard($game);
            $message = $player->getUsername() .
                GlenmoreTranslation::RESOURCE_SELECTED .
                $player->getPersonalBoard()->getResourceToSell()->getId();
            $this->logService->sendPlayerLog($game, $player, $message);
        }
        $player->setActivatedResourceSelection(false);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $this->publishPersonalBoard($player, $player->getPersonalBoard()->getBuyingTile() == null ? [] :
            $this->tileGLMService->verifyAllPositionOnPersonalBoard(
                $player->getPersonalBoard(),
                $player->getPersonalBoard()->getBuyingTile()->getBoardTile()->getTile()
            ));
        $this->publishPersonalBoardSpectator($game);
        $this->publishRanking($game);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername()
            . GlenmoreTranslation::VALIDATE_RESOURCES_SELECTION);
        return new Response(GlenmoreTranslation::RESPONSE_VALIDATE_RESOURCES_SELECTION,
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/cancel/resources/selection',
        name: 'app_game_glenmore_cancel_resources_selection')]
    public function cancelResourcesSelection(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE,
                Response::HTTP_FORBIDDEN);
        }
        $this->tileGLMService->clearResourceSelection($player);
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::CANCEL_SELECTION);
        return new Response(GlenmoreTranslation::RESPONSE_CANCEL_SELECTION,
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/end/player/round', name: 'app_game_glenmore_end_player_round')]
    public function endPlayerRound(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE,
                Response::HTTP_FORBIDDEN);
        }
        $this->service->manageEndOfRound($game);
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishRanking($game);
        $this->publishMainBoardPreview($game);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::END_ROUND);
        return new Response(GlenmoreTranslation::RESPONSE_END_ROUND,
            Response::HTTP_OK);
    }


    #[Route('game/glenmore/{idGame}/displayPersonalBoard/{idPlayer}',
        name: 'app_game_glenmore_display_player_personal_board')]
    public function displayPlayerPersonalBoard(
        #[MapEntity(id: 'idGame')] GameGLM $gameGLM,
        #[MapEntity(id: 'idPlayer')] PlayerGLM $playerGLM): Response
    {
        if ($gameGLM->isPaused() || !$gameGLM->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $personalBoard = $playerGLM->getPersonalBoard();
        return $this->render('Game/Glenmore/MainBoard/playerPersonalBoard.html.twig', [
            'isSpectator' => true,
            'game' => $gameGLM,
            'player' => $playerGLM,
            'personalBoardTiles' => $this->dataManagementGLMService->organizePersonalBoardRows($playerGLM, []),
            'whiskyCount' => $this->dataManagementGLMService->getWhiskyCount($playerGLM),
            'activableTiles' => $this->service->isInActivationPhase($playerGLM) ?
                $this->tileGLMService->getActivableTiles($personalBoard->getPlayerTiles()->last())
                : null,
        ]);
    }

    #[Route('/game/{idGame}/glenmore/show/main/board', name: 'app_game_glenmore_show_main_board')]
    public function showMainBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        return $this->render('Game/Glenmore/MainBoard/mainBoard.html.twig',
            [
                'game' => $game,
                'player' => $this->service->getActivePlayer($game),
                'boardTiles' => $this->dataManagementGLMService->organizeMainBoardRows(
                    $this->dataManagementGLMService->createBoardBoxes($game)),
                'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
                'isSpectator' => $player == null
            ]);
    }

    #[Route('/game/{idGame}/glenmore/cancel/buying/tile', name: 'app_game_glenmore_cancel_buying_tile')]
    public function cancelBuyingTile(
        #[MapEntity(id: 'idGame')] GameGLM $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $this->tileGLMService->clearTileSelection($player);
        $this->tileGLMService->clearResourceSelection($player);
        $this->service->setPhase($player, GlenmoreParameters::BUYING_PHASE);
        $player->setActivatedResourceSelection(false);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishMainBoardPreview($game);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::CANCEL_TILE_ACQUISITION);
        return new Response(GlenmoreTranslation::RESPONSE_CANCEL_TILE_ACQUISITION,
            Response::HTTP_OK);
    }

    #[Route('/game/{idGame}/glenmore/cancel/activating/tile', name: 'app_game_glenmore_cancel_activating_tile')]
    public function cancelActivatingTile(
        #[MapEntity(id: 'idGame')] GameGLM $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE,
                Response::HTTP_FORBIDDEN);
        }
        $this->tileGLMService->clearTileActivationSelection($player);
        $this->tileGLMService->clearResourceSelection($player);
        $this->service->setPhase($player, GlenmoreParameters::ACTIVATION_PHASE);
        $player->setActivatedResourceSelection(false);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $this->publishPersonalBoard($player, []);
        $this->publishPersonalBoardSpectator($game);
        $this->publishMainBoardPreview($game);
        $this->publishPlayerRoundManagement($game);
        $this->logService->sendPlayerLog($game, $player,
            $player->getUsername() . GlenmoreTranslation::CANCEL_TILE_ACTIVATION);
        return new Response(GlenmoreTranslation::RESPONSE_CANCEL_TILE_ACTIVATION,
            Response::HTTP_OK);
    }

    /**
     * publishCreateResource : send a mercure notification with information regarding the creation of resource
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function publishCreateResource(PlayerTileGLM $playerTileGLM) : void
    {
        $player = $playerTileGLM->getPersonalBoard()->getPlayerGLM();
        $game = $player->getGameGLM();
        $activatedActivationPhase = $playerTileGLM->getTile()->getName() == GlenmoreParameters::CARD_IONA_ABBEY;
        $response = $this->render('Game/Glenmore/PersonalBoard/selectTile.html.twig',
        [
            'player' => $player,
            'selectedTile' => $playerTileGLM,
            'game' => $game,
            'activatedResourceSelection' => $player->isActivatedResourceSelection(),
            'activatedSellingPhase' => $this->service->isInSellingPhase($player),
            'selectedResources' => $player->getPersonalBoard()->getSelectedResources(),
            'activatedNewResourceAcquisition' => $player->isActivatedNewResourcesAcqusition(),
            'chosenNewResources' => $player->getPersonalBoard()->getCreatedResources(),
            'activatedMovementPhase' => $this->service->isInMovementPhase($player),
            'activatedActivationPhase' => $activatedActivationPhase,
            'buyingTile' => $player->getPersonalBoard()->getBuyingTile()
        ]
        );
        $this->publishService->publish(
            $this->generateUrl('app_game_show_glm',
                ['id' => $game->getId()]).'createResource' . $player->getId(),
                $response);
    }

    /**
     * publishCreateResource : send a mercure notification with information regarding the creation of resource
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function publishSelectResource(PlayerTileGLM $playerTileGLM) : void
    {
        $player = $playerTileGLM->getPersonalBoard()->getPlayerGLM();
        $game = $player->getGameGLM();
        $response = $this->render('Game/Glenmore/PersonalBoard/selectTile.html.twig',
            [
                'player' => $player,
                'selectedTile' => $playerTileGLM,
                'game' => $game,
                'activatedSellingPhase' => $this->service->isInSellingPhase($player),
                'selectedResources' => $player->getPersonalBoard()->getSelectedResources(),
                'activatedNewResourceAcquisition' => $player->isActivatedNewResourcesAcqusition(),
                'chosenNewResources' => $player->getPersonalBoard()->getCreatedResources(),
                'activatedMovementPhase' => $this->service->isInMovementPhase($player),
                'activatedActivationPhase' => $this->service->isInActivationPhase($player),
                'activatedBuyingPhase' => $this->service->isInBuyingPhase($player),
                'activatedResourceSelection' => $player->isActivatedResourceSelection(),
                'buyingTile' => $player->getPersonalBoard()->getBuyingTile()
            ]
        );
        $this->publishService->publish(
            $this->generateUrl('app_game_show_glm',
                ['id' => $game->getId()]).'selectResource' . $player->getId(),
            $response);
    }

    /**
     * publishMainBoard: publish with mercure the main board
     * @param GameGLM $game
     * @return void
     */
    private function publishMainBoardPreview(GameGLM $game) : void
    {
        $players = $game->getPlayers();
        foreach ($players as $player) {
            $response = $this->render('Game/Glenmore/MainBoard/preview.html.twig',
                [
                    'game' => $game,
                    'player' => $this->service->getActivePlayer($game),
                    'boardTiles' => $this->dataManagementGLMService->organizeMainBoardRows(
                        $this->dataManagementGLMService->createBoardBoxes($game)),
                    'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
                    'isSpectator' => $player == null,
                    'currentDrawTile' => $this->tileGLMService->getActiveDrawTile($game),
                ]);
            $this->publishService->publish(
                $this->generateUrl('app_game_show_glm',
                    ['id' => $game->getId()]).'mainBoardPreview'.$player->getId(),
                    $response
            );
        }
    }

    /**
     * publishPersonalBoard: publish with mercure the personal board of the player
     * @param PlayerGLM $player
     * @return void
     */
    private function publishPersonalBoard(PlayerGLM $player, array $possiblePlacement) : void
    {
        $response = $this->render('Game/Glenmore/PersonalBoard/personalBoard.html.twig', [
            'isSpectator' => $player === null,
            'game' => $player->getGameGLM(),
            'player' => $player,
            'activableTiles' => $this->service->isInActivationPhase($player) ?
                $this->tileGLMService->getActivableTiles($player->getPersonalBoard()->getPlayerTiles()->last())
                : null,
            'activatedResourceSelection' => $player->isActivatedResourceSelection(),
            'personalBoardTiles' => $this->dataManagementGLMService
                ->organizePersonalBoardRows($player, $possiblePlacement),
            'whiskyCount' => $this->dataManagementGLMService->getWhiskyCount($player),
        ]);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_glm',
                ['id' => $player->getGameGLM()->getId()]).'personalBoard'.$player->getId(),
            $response
        );
    }

    /**
     * publishPersonalBoardSpectator: publish with mercure the personal board of the players for spectators
     * @param GameGLM $game
     * @return void
     */
    private function publishPersonalBoardSpectator(GameGLM $game) : void
    {
        $possiblePlacement = [];
        foreach($game->getPlayers() as $player) {
            $response = $this->render('Game/Glenmore/MainBoard/playerPersonalBoard.html.twig', [
                'isSpectator' => $player === null,
                'game' => $player->getGameGLM(),
                'player' => $player,
                'activableTiles' => $this->service->isInActivationPhase($player) ?
                    $this->tileGLMService->getActivableTiles($player->getPersonalBoard()->getPlayerTiles()->last())
                    : null,
                'activatedResourceSelection' => $player->isActivatedResourceSelection(),
                'personalBoardTiles' => $this->dataManagementGLMService->organizePersonalBoardRows(
                    $player,
                    $possiblePlacement
                ),
                'whiskyCount' => $this->dataManagementGLMService->getWhiskyCount($player),
            ]);
            $this->publishService->publish(
                $this->generateUrl('app_game_show_glm',
                    ['id' => $player->getGameGLM()->getId()]).'personalBoardSpectator'.$player->getId(),
                $response
            );
        }
    }

    /**
     * publishPlayerRoundManagement : send a mercure notification to update player buttons
     * @param GameGLM $game
     * @return void
     */
    private function publishPlayerRoundManagement(GameGLM $game) : void
    {
        foreach ($game->getPlayers() as $player) {
            $response = $this->render('Game/Glenmore/MainBoard/playerRoundManagement.html.twig', [
                'game' => $game,
                'player' => $player,
                'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
                'isSpectator' => $player == null,
                'activatedResourceSelection' => $player->isActivatedResourceSelection(),
                'activatedNewResourceAcquisition' => $player->isActivatedNewResourcesAcqusition(),
                'activatedMovementPhase' => $this->service->isInMovementPhase($player),
                'activatedSellingPhase' => $this->service->isInSellingPhase($player),
                'activatedActivationPhase' => $this->service->isInActivationPhase($player),
                'activatedBuyingPhase' => $this->service->isInBuyingPhase($player),
                'activableTiles' => $this->tileGLMService->getActivableTiles(
                    $player->getPersonalBoard()->getPlayerTiles()->last()
                ),
            ]);
            $this->publishService->publish(
                $this->generateUrl('app_game_show_glm',
                    ['id' =>$game->getId()]).'playerRoundManagement'.$player->getId(),
                $response
            );
        }
    }

    /**
     * publishMainBoard: publish with mercure the main board
     * @param GameGLM $game
     * @return void
     */
    private function publishMainBoard(GameGLM $game) : void
    {
        $players = $game->getPlayers();
        foreach ($players as $player) {
            $response = $this->render('Game/Glenmore/MainBoard/mainBoard.html.twig',
                [
                    'game' => $game,
                    'player' => $this->service->getActivePlayer($game),
                    'boardTiles' => $this->dataManagementGLMService->organizeMainBoardRows(
                        $this->dataManagementGLMService->createBoardBoxes($game)),
                    'needToPlay' => $player == null ? false : $player->isTurnOfPlayer(),
                    'isSpectator' => $player == null,
                    'currentDrawTile' => $this->tileGLMService->getActiveDrawTile($game),
                ]);
            $this->publishService->publish(
                $this->generateUrl('app_game_show_glm',
                    ['id' => $game->getId()]).'mainBoard'.$player->getId(),
                $response
            );
        }
    }

    /**
     * publishRanking : send a mercure notification to each player about their ranking
     * @param GameGLM $game
     * @return void
     */
    private function publishRanking(GameGLM $game) : void
    {
        foreach ($game->getPlayers() as $player) {
            $response = $this->render('Game/Glenmore/Ranking/ranking.html.twig', [
                'game' => $game,
                'playersDataResources' => $this->dataManagementGLMService->getPlayersResourcesData($game),
                'currentDrawTile' => $this->tileGLMService->getActiveDrawTile($game),
                'player' => $player
            ]);
            $this->publishService->publish(
                $this->generateUrl('app_game_show_glm',
                    ['id' =>$game->getId()]).'ranking'.$player->getId(),
                $response
            );
        }
    }

    /**
     * publishMoveVillagerOnPersonnalBoard : send a mercure notification to a specific player
     * who moved a villager on his personnal board
     * @param GameGLM $game
     * @param PlayerGLM $player
     * @param PlayerTileGLM $originTile
     * @param PlayerTileGLM $targetedTile
     * @return void
     */
    private function publishMoveVillagerOnPersonalBoard(
        GameGLM $game, PlayerGLM $player,
        PlayerTileGLM $originTile, PlayerTileGLM $targetedTile
    ) : void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_glm', ['id' => $game->getId()])
            .'animVillagerMovement'.$player->getId(),
            new Response($originTile->getTile()->getId() . '_' . $targetedTile->getTile()->getId())
        );
    }

    private function publishNotification(GameGLM $game, int $duration, string $message,
                                         string $description, string $iconId,
                                         string $loadingBarColor, string $targetedPlayer): void
    {
        $dataSent =  [$duration, $message, $description, $iconId, $loadingBarColor];

        $this->publishService->publish(
            $this->generateUrl('app_game_show_glm', ['id' => $game->getId()]).'notification'.$targetedPlayer,
            new Response(implode('_', $dataSent))
        );
    }

    private function typeResources(string $color): string
    {
        return GlenmoreTranslation::RESOURCE_DESC[$color];
    }


}
