<?php

namespace App\Controller\Game;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Splendor\GameSPL;
use App\Repository\Game\Glenmore\BoardTileGLMRepository;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Service\Game\Glenmore\CardGLMService;
use App\Service\Game\Glenmore\DataManagementGLMService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use App\Service\Game\Glenmore\WarehouseGLMService;
use App\Service\Game\LogService;
use App\Service\Game\MessageService;
use App\Service\Game\PublishService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
                                private CardGLMService $cardGLMService,
                                private LogService $logService,
                                private ResourceGLMRepository $resourceGLMRepository,
                                private PublishService $publishService,
                                private WarehouseGLMService $warehouseGLMService,
                                private EntityManagerInterface $entityManager,
                                )
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
            'activableTiles' => $this->tileGLMService->getActivableTiles($personalBoard->getPlayerTiles()->last()),
            'potentialNeighbours' => null,
            'currentDrawTile' => $this->tileGLMService->getActiveDrawTile($game),
            'personalBoardTiles' => $this->dataManagementGLMService->organizePersonalBoardRows($player),
            'boardTiles' => $this->dataManagementGLMService->organizeMainBoardRows(
                $this->dataManagementGLMService->createBoardBoxes($game)),
            'whiskyCount' => $this->dataManagementGLMService->getWhiskyCount($player),
            'activatedBuyingPhase' => $buyingPhase,
            'activatedActivationPhase' => $activationPhase,
            'activatedSellingPhase' => $sellingPhase,
            'activatedMovementPhase' => $movementPhase,
            'activatedResourceSelection' => $player->isActivatedResourceSelection(),
            'selectedResources' => $player->getPersonalBoard()->getSelectedResources(),
            'activatedNewResourceAcquisition' => false,
            'chosenNewResources' => $player->getPersonalBoard()->getCreatedResources(),
            'selectedWarehouseProduction' => null,
            'isWarehouseMoneySelected' => false,
            'buyingTile' => $player->getPersonalBoard()->getBuyingTile(),
            'messages' => $messages,
        ]);
    }

    #[Route('game/glenmore/{idGame}/display/propertyCards',
            name: 'app_game_glenmore_display_player_property_cards')]
    public function displayPropertyCards(
        #[MapEntity(id: 'idGame')] GameGLM $gameGLM): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($gameGLM, $this->getUser()->getUsername());
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
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        if ($this->service->getActivePlayer($game) !== $player) {
            return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
        }
        try {
            $this->warehouseGLMService->buyResourceFromWarehouse($player, $line->getResource());
        } catch (Exception $e) {
            echo($e->getMessage());
            $message = $player->getUsername() . " tried to buy resource " . $line->getResource()->getId()
                . " but could not afford it";
            //$this->logService->sendPlayerLog($game, $player, $message);
            return new Response("can't afford this resource", Response::HTTP_FORBIDDEN);
        }
        // TODO Publish management
        $message = $player->getUsername() . " chose resource " . $line->getResource()->getId();
        //$this->logService->sendPlayerLog($game, $player, $message);
        return new Response('player bought this resource', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/sell/resource/warehouse/production/mainBoard/{idResourceLine}',
        name: 'app_game_glenmore_sell_resource_warehouse_production_on_mainboard')]
    public function sellResourceWarehouseProductionOnMainBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idResourceLine')] WarehouseLineGLM $line
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
            $this->warehouseGLMService->sellResource($player, $line->getResource());
        } catch (Exception) {
            $message = $player->getUsername() . " tried to sell resource " . $line->getResource()->getId()
                . " but could not do it";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("can't sell this resource", Response::HTTP_FORBIDDEN);
        }
        // TODO Publish management
        $message = $player->getUsername() . " chose resource " . $line->getResource()->getId();
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('player sold this resource', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/tile/mainBoard/{idTile}',
            name: 'app_game_glenmore_select_tile_on_mainboard')]
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
        if($this->tileGLMService->hasBuyCost($tile)) {
            $player->setActivatedResourceSelection(true);
            $player->setRoundPhase(GlenmoreParameters::$BUYING_PHASE);
        } else {
            $player->setRoundPhase(GlenmoreParameters::$ACTIVATION_PHASE);
            $player->setActivatedResourceSelection(false);
        }
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        try {
            $this->tileGLMService->assignTileToPlayer($tile, $player);
        } catch (Exception $e) {
            $message = $player->getUsername() . " tried to choose tile " . $tile->getId()
                . " but could not afford it";
            $this->logService->sendPlayerLog($game, $player, $message);
            return new Response("can't afford this tile" . $e->getMessage(), Response::HTTP_FORBIDDEN);
        }
        // TODO Publish management
        $message = $player->getUsername() . " chose tile " . $tile->getId();
        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('player selected this tile', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/tile/mainBoard/{coordX}/{coordY}',
        name: 'app_game_glenmore_put_tile_on_personal_board')]
    public function putTileOnPersonalBoard(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        int $coordX,
        int $coordY
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
            $this->tileGLMService->setPlaceTileAlreadySelected($player, $coordX, $coordY);
        } catch (Exception $e) {
            //$this->logService->sendPlayerLog($game, $player, $message);
            return new Response("can't place this tile" . $e, Response::HTTP_FORBIDDEN);
        }
        $this->service->setPhase($player, GlenmoreParameters::$ACTIVATION_PHASE);
        $player->setActivatedResourceSelection(false);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->last();
        if ($this->tileGLMService->giveBuyBonus($playerTile) == -1) {
             $this->publishCreateResource($playerTile);
        }
        // TODO Publish management
//        $message = $player->getUsername() . " put tile " . $player->getPersonalBoard()->getSelectedTile()->getId();
//        $this->logService->sendPlayerLog($game, $player, $message);
        return new Response('player put this tile', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/tile/personalBoard/{idTile}',
            name: 'app_game_glenmore_select_tile_on_personalboard')]
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
            'activatedBuyingPhase' => $this->service->isInBuyingPhase($player),
            'activatedActivationPhase' => $this->service->isInActivationPhase($player),
            'activatedSellingPhase' => $this->service->isInSellingPhase($player),
            'selectedResources' => $player->getPersonalBoard()->getSelectedResources(),
            'activatedResourceSelection' => $player->isActivatedResourceSelection(),
            'activatedNewResourceAcquisition' => false,
            'chosenNewResources' => $player->getPersonalBoard()->getCreatedResources(),
            'activatedMovementPhase' => $this->service->isInMovementPhase($player),
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
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $phase = $player->getRoundPhase();
        if ($phase == GlenmoreParameters::$BUYING_PHASE) {
            try {
                $this->tileGLMService->selectResourcesFromTileToBuy($tile, $resourceGLM->getResource());
            } catch (\Exception $e) {
                return new Response($e->getMessage(), Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
            }
        } else if ($phase == GlenmoreParameters::$ACTIVATION_PHASE) {
            try {
                $this->tileGLMService->selectResourcesFromTileToActivate($tile, $resourceGLM->getResource());
            } catch (\Exception $e) {
            }
        }
        return new Response('a new resource has been selected', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/select/{idTile}/resource/acquisition/{resource}',
            name: 'app_game_glenmore_select_new_resource_acquisition')]
    public function selectNewResourceAcquisition(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile,
        string $resource
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $production_resource = $this->resourceGLMRepository->findOneBy(["type" => $resource]);
        if($tile->getTile()->getName() === GlenmoreParameters::$CARD_LOCH_LOCHY) {
            try {
                $this->cardGLMService->selectResourceForLochLochy($player, $production_resource);
            } catch (\Exception) {
                return new Response('can not select more resource', Response::HTTP_FORBIDDEN);
            }
        }
        return new Response($player->getUsername()." selected a resource" ,Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/remove/{idTile}/villager/{idPlayerTileResource}',
            name: 'app_game_glenmore_remove_villager')]
    public function removeVillager(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idPlayerTileResource')] PlayerTileResourceGLM $resourceGLM,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
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
            $this->tileGLMService->removeVillager($tile);
        } catch (Exception) {
            return new Response('Invalid move', Response::HTTP_FORBIDDEN);
        }
        return new Response('villager has been removed', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/activate/{idTile}', name: 'app_game_glenmore_activate_tile')]
    public function activateTile(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $this->tileGLMService->chooseTileToActivate($tile);
        if ($this->service->getActivePlayer($game) !== $player) {
            return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
        }
        if(!$this->tileGLMService->hasActivationCost($tile)) {
            $player->setActivatedResourceSelection(false);
            try {
                $this->tileGLMService->activateBonus($tile, $player);
            } catch (\Exception $e) {
            }
        } else {
            $this->service->setPhase($player, GlenmoreParameters::$ACTIVATION_PHASE);
            $player->setActivatedResourceSelection(true);
        }
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        return new Response('tile was activated', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/validate/activation', name: 'app_game_glenmore_validate_activation_tile')]
    public function validateActivationTile(
        #[MapEntity(id: 'idGame')] GameGLM $game,
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $tile = $player->getPersonalBoard()->getActivatedTile();
        try {
            $this->tileGLMService->activateBonus($tile, $player);
        } catch (\Exception $e) {
        }
        $player->setActivatedResourceSelection(false);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        return new Response("tile was activated", Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/end/activation', name: 'app_game_glenmore_end_activate_tile')]
    public function endTileActivation(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $this->service->setPhase($player, GlenmoreParameters::$MOVEMENT_PHASE);
        return new Response($player->getUsername().' has ended activation phase', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/move/{idTile}/villager/direction/{dir}',
            name: 'app_game_glenmore_move_villager')]
    public function moveVillager(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile,
        int $dir
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
            $this->tileGLMService->moveVillager($tile, $dir);
        } catch (Exception) {
            return new Response('Could not move a villager from this tile to 
                targeted one ');
        }
        return new Response('the villager has been moved', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/validate/{idTile}/resource/acquisition',
            name: 'app_game_glenmore_validate_new_resources_acquisition')]
    public function validateNewResourcesAcquisition(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        if($tile->getTile()->getName() === GlenmoreParameters::$CARD_LOCH_LOCHY) {
            $this->cardGLMService->validateTakingOfResourcesForLochLochy($player);
        }
        return new Response($player->getUsername().' has ended new resources acquisition phase',
            Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/cancel/{idTile}/resource/acquisition',
            name: 'app_game_glenmore_cancel_new_resources_acquisition')]
    public function cancelNewResourcesAcquisition(
        #[MapEntity(id: 'idGame')] GameGLM $game,
        #[MapEntity(id: 'idTile')] PlayerTileGLM $tile
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        if($tile->getTile()->getName() === GlenmoreParameters::$CARD_LOCH_LOCHY) {
            $this->cardGLMService->clearCreatedResources($player);
        }
        return new Response('the chosen resources have been canceled', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/validate/resources/selection',
            name: 'app_game_glenmore_validate_resources_selection')]
    public function validateResourcesSelection(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $playerPhase = $player->getRoundPhase();
        if ($playerPhase == GlenmoreParameters::$BUYING_PHASE) {
            try {
                $this->tileGLMService->setPlaceTileAlreadySelected($player,
                    $player->getPersonalBoard()->getBuyingTile()->getCoordX(),
                    $player->getPersonalBoard()->getBuyingTile()->getCoordY());
            } catch (Exception $e) {
                return new Response('player has not selected needed resources', Response::HTTP_FORBIDDEN);
            }
            $this->service->setPhase($player, GlenmoreParameters::$ACTIVATION_PHASE);
            $player->setActivatedResourceSelection(false);
        }
        return new Response('player selected resources', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/cancel/resources/selection',
        name: 'app_game_glenmore_cancel_resources_selection')]
    public function cancelResourcesSelection(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $this->tileGLMService->clearResourceSelection($player);
        return new Response('player cancel his selection', Response::HTTP_OK);
    }

    #[Route('game/glenmore/{idGame}/end/player/round', name: 'app_game_glenmore_end_player_round')]
    public function endPlayerRound(
        #[MapEntity(id: 'idGame')] GameGLM $game
    )  : Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $this->service->manageEndOfRound($game);
        return new Response('player ended activation of his tiles', Response::HTTP_OK);
    }


    #[Route('game/glenmore/{idGame}/displayPersonalBoard/{idPlayer}', name: 'app_game_glenmore_display_player_personal_board')]
    public function displayPlayerPersonalBoard(
        #[MapEntity(id: 'idGame')] GameGLM $gameGLM,
        #[MapEntity(id: 'idPlayer')] PlayerGLM $playerGLM): Response
    {
        return $this->render('Game/Glenmore/PersonalBoard/personalBoard.html.twig', [
            'isSpectator' => true,
            'player' => $playerGLM,
            'personalBoardTiles' => $this->dataManagementGLMService->organizePersonalBoardRows($playerGLM),
            'whiskyCount' => $this->dataManagementGLMService->getWhiskyCount($playerGLM),
        ]);
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
        // TODO
        $response = $this->render('',
        []
        );
        $this->publishService->publish(
            $this->generateUrl('app_game_show_glm',
                ['id' => $game->getId()]).'createResource' . $player->getId(),
                $response);
    }

}