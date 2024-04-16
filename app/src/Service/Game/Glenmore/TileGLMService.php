<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\BuyingTileGLM;
use App\Entity\Game\Glenmore\CreatedResourceGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerCardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\SelectedResourceGLM;
use App\Entity\Game\Glenmore\TileActivationCostGLM;
use App\Entity\Game\Glenmore\TileBuyCostGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileResourceGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\SelectedResourceGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Doctrine\ORM\EntityManagerInterface;

class TileGLMService
{

    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly ResourceGLMRepository $resourceGLMRepository,
                                private readonly PlayerGLMRepository $playerGLMRepository,
                                private readonly PlayerTileResourceGLMRepository $playerTileResourceGLMRepository,
                                private readonly PlayerTileGLMRepository $playerTileGLMRepository,
                                private readonly CardGLMService $cardGLMService,
                                private readonly SelectedResourceGLMRepository $selectedResourceGLMRepository) {}

    /**
     * getAmountOfTileToReplace : returns the amount of tiles to replace
     * @param MainBoardGLM $mainBoardGLM
     * @return int
     */
    public function getAmountOfTileToReplace(MainBoardGLM $mainBoardGLM): int
    {
        $this->removeTilesOfBrokenChain($mainBoardGLM);
        $player = $this->getActivePlayer($mainBoardGLM ->getGameGLM());
        $playerPosition = $player->getPawn()->getPosition();
        $pointerPosition = $playerPosition - 2;
        if($pointerPosition < 0){
            $pointerPosition += GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
        }
        $count = 0;
        while ($this->getTileInPosition($mainBoardGLM, $pointerPosition) == null
        && $this->getPawnsAtPosition($player, $mainBoardGLM, $pointerPosition) == null){
            $pointerPosition -= 1;
            $count += 1;
            if($pointerPosition < 0){
                $pointerPosition += GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
            }
        }
        return $count;
    }

    /**
     * hasActivationCost : return if the player tile has an activation cost (need resources selection from the player)
     * @param PlayerTileGLM $tileGLM
     * @return bool
     */
    public function hasActivationCost(PlayerTileGLM $tileGLM) : bool
    {
        return !$tileGLM->getTile()->getActivationPrice()->isEmpty();
    }

    /**
     * hasBuyCost : return if the player need to select resources for buying tile
     * @param BoardTileGLM $tileGLM
     * @return bool
     */
    public function hasBuyCost(BoardTileGLM $tileGLM) : bool
    {
        if ($tileGLM->getTile()->getName() === GlenmoreParameters::$CARD_LOCH_OICH) {
            return true;
        }
        $buyPrice = $tileGLM->getTile()->getBuyPrice();

        if ($buyPrice->first() != null && $buyPrice->first()->getResource()->getType() == GlenmoreParameters::$WHISKY_RESOURCE) {
            return false;
        }
        return !$tileGLM->getTile()->getBuyPrice()->isEmpty();
    }

    /**
     * getActiveDrawTile : returns the draw tile with the lowest level which is not empty
     *                      or null if all draw tiles are empty
     * @param GameGLM $gameGLM
     * @return DrawTilesGLM|null
     */
    public function getActiveDrawTile(GameGLM $gameGLM) : ?DrawTilesGLM
    {
        $mainBoard = $gameGLM->getMainBoard();
        $drawTiles = $mainBoard->getDrawTiles();
        for ($i = GlenmoreParameters::$TILE_LEVEL_ZERO; $i <= GlenmoreParameters::$TILE_LEVEL_THREE; ++$i) {
            $draw = $drawTiles->get($i);
            if (!$draw->getTiles()->isEmpty()) {
                return $draw;
            }
        }
        return null;
    }

    /**
     * getActivableTiles : returns a collection of all activable tiles after a new tile was placed
     *  onto personalBoard
     * @param PlayerTileGLM $playerTileGLM
     * @return ArrayCollection<Int, PlayerTileGLM>
     */
    public function getActivableTiles(PlayerTileGLM $playerTileGLM) : ArrayCollection
    {
        $activableTiles = new ArrayCollection();
        if (!$playerTileGLM->isActivated() && !$playerTileGLM->getTile()->getActivationBonus()->isEmpty()) {
            $activableTiles->add($playerTileGLM);
        }
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $lastTile = $personalBoard->getPlayerTiles()->last()->getTile();
        $card = $lastTile->getCard();
        // if player just bought Loch Oich then all tiles can be activated
        if ($card != null && $card->getName() === GlenmoreParameters::$CARD_LOCH_OICH) {
            $adjacentTiles = $personalBoard->getPlayerTiles();
        } else { // else just adjacent tiles can be activated
            $adjacentTiles = $playerTileGLM->getAdjacentTiles();
        }
        foreach ($adjacentTiles as $adjacentTile) {
            if (!$adjacentTile->isActivated() && !$adjacentTile->getTile()->getActivationBonus()->isEmpty()) {
                $activableTiles->add($adjacentTile);
            }
        }

        // if every tile has been activated
        if ($activableTiles->isEmpty()) {
            $activableTiles = $this->cardGLMService->applyLochNess($personalBoard);
        }
        // if there is no activable tiles even after Loch Ness
        if($activableTiles->isEmpty()) {
            if ($personalBoard->getPlayerGLM()->getRoundPhase() != GlenmoreParameters::$SELLING_PHASE) {
                $personalBoard->getPlayerGLM()->setRoundPhase(GlenmoreParameters::$MOVEMENT_PHASE);
                $this->entityManager->persist($personalBoard->getPlayerGLM());
                $this->entityManager->flush();
            }
        }
        return $activableTiles;
    }

    /**
     * canPlaceTile: return true if the player can place the selected tile at the chosen emplacement
     * @param int $x the coord x of the wanted emplacement
     * @param int $y the coord y of the wanted emplacement
     * @param TileGLM $tile
     * @param PlayerGLM $player
     * @return bool
     */
    public function canPlaceTile(int $x, int $y, TileGLM $tile, PlayerGLM $player): bool
    {
        //Recovery of the adjacent tiles
        $tileLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileRight = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x, 'coord_Y' => $y + 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileUp = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileDown = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x + 1, 'coord_Y' => $y
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileUpLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileUpRight = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y + 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileDownLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x + 1, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileDownRight = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x + 1, 'coord_Y' => $y + 1
            , 'personalBoard' => $player->getPersonalBoard()]);

        //Verification of the constraints
        return $this->verifyRoadAndRiverConstraints($tile, $tileLeft, $tileRight, $tileUp, $tileDown)
            && $this->isVillagerInAdjacentTiles(
                new ArrayCollection(
                    [$tileLeft, $tileRight, $tileUp, $tileDown,
                        $tileUpLeft, $tileUpRight, $tileDownLeft, $tileDownRight]
                )
            );
    }

    /**
     * canBuyTile: return true if the player can buy the tile
     * @param TileGLM $tile
     * @param PlayerGLM $player
     * @return bool
     */
    public function canBuyTile(TileGLM $tile, PlayerGLM $player) : bool
    {
        $globalResources = $player->getPlayerTileResourceGLMs();
        $money = $player->getPersonalBoard()->getMoney();
        $game = $player->getGameGLM();
        $warehouse = $game->getMainBoard()->getWarehouse();
        foreach ($tile->getBuyPrice() as $buyPrice) {
            $resourceTile = $buyPrice->getResource();
            $warehouseLines = $warehouse->getWarehouseLine();
            $line = null;
            foreach ($warehouseLines as $warehouseLine) {
                if ($warehouseLine->getResource() === $resourceTile) {
                    $line = $warehouseLine;
                    break;
                }
            }
            $priceTile = $buyPrice->getPrice();
            $resourcesOfPlayerLikeResourceTile = $globalResources->filter(
                function (PlayerTileResourceGLM $playerTileResource) use ($resourceTile) {
                    return $playerTileResource->getResource()->getId() == $resourceTile->getId();
                }
            );
            $quantity = 0;
            foreach ($resourcesOfPlayerLikeResourceTile as $resource) {
                $quantity += $resource->getQuantity();
            }
            if ($quantity < $priceTile) {
                $remaining = $priceTile - $quantity;
                for ($i = 0; $i < $remaining; ++$i) {
                    $quantity = $line->getQuantity();
                    if ($quantity == 3) {
                        return false;
                    }
                    $neededMoney = GlenmoreParameters::$MONEY_FROM_QUANTITY[$quantity];
                    $money -= $neededMoney;
                    if ($money < 0) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * canBuyLochOich: return true if the player can buy Loch Oich
     * @param TileGLM $tile
     * @param PlayerGLM $player
     * @return bool
     */
    public function canBuyLochOich(TileGLM $tile, PlayerGLM $player) : bool
    {
        $playerTiles = $player->getPersonalBoard()->getPlayerTiles();
        $playerResources = new ArrayCollection();
        $warehouse = $player->getGameGLM()->getMainBoard()->getWarehouse();
        $money = $player->getPersonalBoard()->getMoney();
        foreach ($playerTiles as $playerTile) {
            foreach ($playerTile->getPlayerTileResource() as $playerTileResource) {
                if ($playerTileResource->getQuantity() == 0) {
                    continue;
                }
                if ($playerTileResource->getResource()->getType() != GlenmoreParameters::$PRODUCTION_RESOURCE) {
                    continue;
                }
                if (!$playerResources->contains($playerTileResource->getResource())) {
                    $playerResources->add($playerTileResource->getResource());
                }
            }
        }
        if ($playerResources->count() >= 2) {
            return true;
        }
        $i = 0;
        while ($i < 5) {
            $line = $warehouse->getWarehouseLine()->get($i);
            ++$i;
            if ($playerResources->contains($line->getResource())) {
                continue;
            }
            if ($line->getQuantity() == 3) {
                continue;
            }
            $neededMoney = GlenmoreParameters::$MONEY_FROM_QUANTITY[$line->getQuantity()];
            $money -= $neededMoney;
            if ($money < 0) {
                $money += $neededMoney;
                continue;
            }
            $playerResources->add($line->getResource());
            if ($playerResources->count() == 2) {
                return true;
            }
        }
        return false;
    }


    /**
     * giveBuyBonus : once bought and placed, the playerTile gives bonus to its player
     * @param PlayerTileGLM $playerTileGLM
     * @return int
     */
    public function giveBuyBonus(PlayerTileGLM $playerTileGLM) : int
    {
        $returnCode = 0;
        $tile = $playerTileGLM->getTile();
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $buyBonus = $tile->getBuyBonus();
        foreach ($buyBonus as $bonus) {
            $playerTileResource = new PlayerTileResourceGLM();
            $playerTileResource->setPlayerTileGLM($playerTileGLM);
            $playerTileResource->setPlayer($playerTileGLM->getPersonalBoard()->getPlayerGLM());
            $playerTileResource->setResource($bonus->getResource());
            $playerTileResource->setQuantity($bonus->getAmount());
            $this->entityManager->persist($playerTileResource);
            $playerTileGLM->addPlayerTileResource($playerTileResource);
            $this->entityManager->persist($playerTileGLM);
        }
        $card = $tile->getCard();
        if ($card != null) {
            $playerCard = new PlayerCardGLM($personalBoard, $card);
            $this->entityManager->persist($playerCard);
            $personalBoard->addPlayerCardGLM($playerCard);
            $this->entityManager->persist($personalBoard);
            $cardBonus = $card->getBonus();
            if ($cardBonus != null) {
                $resource = $cardBonus->getResource();
                $playerTileResource = new PlayerTileResourceGLM();
                $playerTileResource->setQuantity($cardBonus->getAmount());
                $playerTileResource->setResource($resource);
                $playerTileResource->setPlayer($playerTileGLM->getPersonalBoard()->getPlayerGLM());
                $this->entityManager->persist($playerTileResource);
                $playerTileGLM->addPlayerTileResource($playerTileResource);
                $this->entityManager->persist($playerTileGLM);
            }
            $this->entityManager->flush();
            $returnCode = $this->cardGLMService->buyCardManagement($playerTileGLM);
        }
        $this->giveAllMovementPoints($playerTileGLM);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        return $returnCode;
    }

    /**
     * getMovementPoints : returns total movement points of a player
     * @param PlayerGLM $playerGLM
     * @return int
     */
    public function getMovementPoints(PlayerGLM $playerGLM) : int
    {
        $tiles = $playerGLM->getPersonalBoard()->getPlayerTiles();
        $movementPoint = 0;
        foreach ($tiles as $tile) {
            foreach ($tile->getPlayerTileResource() as $tileResource) {
                if ($tileResource->getResource()->getType() === GlenmoreParameters::$MOVEMENT_RESOURCE) {
                    $movementPoint += $tileResource->getQuantity();
                }
            }
        }
        return $movementPoint;
    }


    /**
     * getPlayerProductionResources : return an array containing the count of each resource production of a player
     * @param PlayerGLM $playerGLM
     * @return array<string, int>
     */
    public function getPlayerProductionResources(PlayerGLM $playerGLM) : array
    {
        $result = [];
        $result[GlenmoreParameters::$COLOR_GREEN] = $this->getProductionResourcesCountByColor($playerGLM,
                                                        GlenmoreParameters::$COLOR_GREEN);
        $result[GlenmoreParameters::$COLOR_YELLOW] = $this->getProductionResourcesCountByColor($playerGLM,
                                                        GlenmoreParameters::$COLOR_YELLOW);
        $result[GlenmoreParameters::$COLOR_BROWN] = $this->getProductionResourcesCountByColor($playerGLM,
                                                        GlenmoreParameters::$COLOR_BROWN);
        $result[GlenmoreParameters::$COLOR_WHITE] = $this->getProductionResourcesCountByColor($playerGLM,
                                                        GlenmoreParameters::$COLOR_WHITE);
        $result[GlenmoreParameters::$COLOR_GREY] = $this->getProductionResourcesCountByColor($playerGLM,
                                                        GlenmoreParameters::$COLOR_GREY);
        return $result;
    }

    /**
     * getProductionResourcesCountByColor : return the count of a production resource
     * @param PlayerGLM $playerGLM
     * @param string $color
     * @return int
     */
    public function getProductionResourcesCountByColor(PlayerGLM $playerGLM, string $color) : int
    {
        $tiles = $playerGLM->getPersonalBoard()->getPlayerTiles();
        $count = 0;
        foreach ($tiles as $tile) {
            $resources = $tile->getPlayerTileResource();
            foreach ($resources as $resource) {
                if($resource->getResource()->getType() == GlenmoreParameters::$PRODUCTION_RESOURCE
                    && $resource->getResource()->getColor() == $color) {
                    $count += $resource->getQuantity();
                }
            }
        }
        return $count;
    }

    /**
     * canBuyTileWithSelectedResources : indicate if the player can buy the tile with his current selected resources
     * @param PlayerGLM $player
     * @param TileGLM $tile
     * @return bool
     */
    public function canBuyTileWithSelectedResources(PlayerGLM $player, TileGLM $tile) : bool
    {
        if ($tile->getName() === GlenmoreParameters::$CARD_LOCH_OICH) {
            return $player->getPersonalBoard()->getSelectedResources()->count() >= 2;
        }
        $globalResources = $player->getPersonalBoard()->getSelectedResources();
        foreach ($tile->getBuyPrice() as $buyPrice) {
            $priceTile = $buyPrice->getPrice();
            $resource = $buyPrice->getResource();
            $selectedResourcesOfSameResource = $globalResources->filter(
                function (SelectedResourceGLM $selectedResourceGLM) use ($resource) {
                    return $selectedResourceGLM->getResource()->getId() == $resource->getId();
                }
            );
            if ($selectedResourcesOfSameResource->count() != $priceTile) {
                return false;
            }
        }
        return true;
    }

    /**
     * activateBonus : activate a tile and give player his resources
     *
     * @param PlayerTileGLM   $tileGLM
     * @param PlayerGLM       $playerGLM
     * @param ArrayCollection $activableTiles
     * @return void
     * @throws Exception
     */
    public function activateBonus(PlayerTileGLM $tileGLM, PlayerGLM $playerGLM, ArrayCollection $activableTiles): void
    {
        if ($tileGLM->getTile()->getName() === GlenmoreParameters::$CARD_IONA_ABBEY) {
            return;
        }
        if (!$activableTiles->contains($tileGLM)) {
            throw new \Exception("can't activate this tile");
        }
        $tile = $tileGLM->getTile();
        $bonusNb = $this->hasPlayerEnoughResourcesToActivate($tileGLM, $playerGLM);
        if($bonusNb == -1){
            throw new \Exception("NOT ENOUGH RESOURCES");
        }

        if(!$this->hasEnoughPlaceToActivate($tileGLM)) {
            throw new \Exception("NOT ENOUGH PLACE ON TILE");
        }
        if($tileGLM->getTile()->getName() === GlenmoreParameters::$TILE_NAME_FAIR) {
            $this->activateFair($tileGLM);
            return;
        }
        if($tileGLM->getTile()->getName() === GlenmoreParameters::$TILE_NAME_GROCER) {
            $this->activateGrocer($tileGLM);
            return;
        }
        if($tile->getType() != GlenmoreParameters::$TILE_TYPE_BROWN){
            $this->givePlayerActivationBonus($tileGLM, $playerGLM);
            $this->entityManager->persist($tileGLM);
        } else {
            $activationBonus = $tile->getActivationBonus()->get($bonusNb);
            $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
            $resourcesTypes = new ArrayCollection();
            foreach ($selectedResources as $selectedResource){
                $resourcesTypes->add($selectedResource->getResource()->getColor());
            }
            $resourcesTypesCount = $resourcesTypes->count();
            $activationCostsLevels = $tileGLM->getTile()->getActivationPrice()->count();
            $selectedLevel = min($resourcesTypesCount, $activationCostsLevels);
            $activationBonus = $tile->getActivationBonus()->get($bonusNb);
            if ($tileGLM->getTile()->getName() === GlenmoreParameters::$TILE_NAME_BRIDGE) {
                $playerGLM->setScore($playerGLM->getScore() + 7);
            } else if ($tileGLM->getTile()->getName() === GlenmoreParameters::$TILE_NAME_BUTCHER
                && !$tileGLM->getTile()->getBuyPrice()->isEmpty()) {
                $playerGLM->setScore($playerGLM->getScore() + 5);
            } else {
                $playerGLM->setScore($playerGLM->getScore() + $activationBonus->getAmount());
            }
            $this->entityManager->persist($playerGLM);
        }
        $tileGLM->setActivated(true);
        $this->entityManager->persist($tileGLM);

        $globalResources = $playerGLM->getPersonalBoard()->getSelectedResources();
        foreach ($tile->getActivationPrice() as $buyPrice) {
            $resourceTile = $buyPrice->getResource();
            $priceTile = $buyPrice->getPrice();
            $resourcesOfPlayerLikeResourceTile = $globalResources->filter(
                function (SelectedResourceGLM $selectedResourceGLM) use ($resourceTile) {
                    return $selectedResourceGLM->getResource()->getId() == $resourceTile->getId();
                }
            );
            foreach ($resourcesOfPlayerLikeResourceTile as $resource) {
                $playerTile = $resource->getPlayerTile();
                if ($playerTile == null) {
                    continue;
                }
                if ($resource->getQuantity() > $priceTile) {
                    $resource->setQuantity($resource->getQuantity() - $priceTile);
                    $playerTileResource = $playerTile->getPlayerTileResource()->filter(
                        function (PlayerTileResourceGLM $playerTileResourceGLM) use ($resource) {
                            return $playerTileResourceGLM->getResource()->getId() == $resource->getResource()->getId();
                        })->first();
                    $playerTileResource->setQuantity($resource->getQuantity() - $priceTile);
                    $this->entityManager->persist($playerTileResource);
                    $priceTile = 0;
                } else {
                    $priceTile -= $resource->getQuantity();
                    $playerGLM->getPersonalBoard()->removeSelectedResource($resource);
                    foreach ($playerTile->getPlayerTileResource() as $item) {
                        if ($item->getResource() === $resource->getResource()) {
                            $item->setQuantity($item->getQuantity() - 1);
                            $this->entityManager->persist($item);
                            break;
                        }
                    }
                }
                if ($priceTile == 0) {
                    break;
                }
            }
        }
        $playerGLM->getPersonalBoard()->setActivatedTile(null);
        $this->entityManager->persist($playerGLM->getPersonalBoard());
        $this->clearResourceSelection($playerGLM);
        $this->entityManager->flush();
    }

    /**
     * selectResourceForIonaAbbey : for Iona Abbey, player picks a resource
     * @param PlayerGLM   $playerGLM
     * @param ResourceGLM $resourceGLM
     * @return void
     * @throws Exception
     */
    public function selectResourceForIonaAbbey(PlayerGLM $playerGLM, ResourceGLM $resourceGLM) : void
    {
        $createdResources = $playerGLM->getPersonalBoard()->getCreatedResources();
        if ($createdResources->count() >= 1) {
            throw new Exception("can't pick more resources");
        }
        $createdResource = new CreatedResourceGLM();
        $createdResource->setResource($resourceGLM);
        $createdResource->setQuantity(1);
        $createdResource->setPersonalBoardGLM($playerGLM->getPersonalBoard());
        $this->entityManager->persist($createdResource);
        $playerGLM->getPersonalBoard()->addCreatedResource($createdResource);
        $this->entityManager->persist($playerGLM->getPersonalBoard());
        $this->entityManager->flush();
    }

    /**
     * validateTakingOfResourcesForIonaAbbey : place the created resource on Iona Abbey tile,
     *  then clears his collection of resources
     *
     * @param PlayerGLM $playerGLM
     * @return void
     * @throws Exception
     */
    public function validateTakingOfResourcesForIonaAbbey(PlayerGLM $playerGLM) : void
    {
        $createdResources = $playerGLM->getPersonalBoard()->getCreatedResources();
        if ($createdResources->count() != 1) {
            throw new Exception("invalid amount of resource selected");
        }
        $tile = null;
        foreach ($playerGLM->getPersonalBoard()->getPlayerTiles() as $playerTile) {
            if ($playerTile->getTile()->getName() === GlenmoreParameters::$CARD_IONA_ABBEY) {
                $tile = $playerTile;
            }
        }
        if ($tile->isActivated()) {
            throw new Exception("tile already selected");
        }
        $count = 0;
        foreach ($tile->getPlayerTileResource() as $resource) {
            $count += $resource->getQuantity();
        }
        if ($count >= 3) {
            throw new Exception("can not add resource on this tile");
        }
        $createdResource = $createdResources->first();
        $exists = false;
        foreach ($tile->getPlayerTileResource() as $tileResource) {
            if ($tileResource->getResource() === $createdResource->getResource()) {
                $tileResource->setQuantity($tileResource->getQuantity() + 1);
                $this->entityManager->persist($tileResource);
                $exists = true;
                $tile->setActivated(true);
                $this->entityManager->persist($tile);
            }
        }
        if (!$exists) {
            $playerTileResource = new PlayerTileResourceGLM();
            $playerTileResource->setPlayer($tile->getPersonalBoard()->getPlayerGLM());
            $playerTileResource->setPlayerTileGLM($tile);
            $playerTileResource->setResource($createdResource->getResource());
            $playerTileResource->setQuantity(1);
            $this->entityManager->persist($playerTileResource);
            $tile->addPlayerTileResource($playerTileResource);
            $tile->setActivated(true);
            $this->entityManager->persist($tile);
        }
        $this->cardGLMService->clearCreatedResources($playerGLM);
    }


    /**
     * selectResourcesFromTileToBuy: select a resources from a tile to use to buy the selectedTile of the player
     * @param PlayerTileGLM $playerTileGLM
     * @param ResourceGLM $resource
     * @return void
     * @throws \Exception if the selectedResources can't be used to buy the tile
     */
    public function selectResourcesFromTileToBuy(PlayerTileGLM $playerTileGLM, ResourceGLM $resource) : void
    {
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $selectedTile = $personalBoard->getBuyingTile();
        $this->addSelectedResourcesFromTileWithCost($personalBoard->getPlayerGLM(), $playerTileGLM, $resource,
            $selectedTile->getBoardTile()->getTile()->getBuyPrice());
    }

    /**
     * selectResourcesFromTileToSellResource: select a resources from a tile to use to sell the resourceToSell
     * @param PlayerTileGLM $playerTileGLM
     * @param ResourceGLM $resource
     * @return void
     * @throws Exception if there is already a selected resource to sell or if the resouce is not the correct type
     */
    public function selectResourcesFromTileToSellResource(PlayerTileGLM $playerTileGLM, ResourceGLM $resource) : void
    {
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $resourceToSell = $personalBoard->getResourceToSell();
        if ($playerTileGLM->getPlayerTileResource()->filter(
            function (PlayerTileResourceGLM $playerTileResourceGLM) use ($resource) {
                return $playerTileResourceGLM->getResource()->getId() == $resource->getId()
                    && $playerTileResourceGLM->getQuantity() > 0;
            })->count() <= 0
        ) {
            throw new Exception("Can't select this resource, there is none of it on this tile");
        }
        if($personalBoard->getSelectedResources()->count() > 0) {
            throw new Exception("Can't select a new resource, already selected one");
        }
        if($resourceToSell->getId() != $resource->getId()) {
            throw new Exception("Can't select this resource, it's not the required type");
        }
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($resource);
        $selectedResource->setPlayerTile($playerTileGLM);
        $selectedResource->setQuantity(1);
        $selectedResource->setPersonalBoardGLM($personalBoard);
        $personalBoard->addSelectedResource($selectedResource);
        $this->entityManager->persist($selectedResource);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * selectLeader: select a leader to use to buy the selectedTile of the player
     * @param PlayerGLM $playerGLM
     * @return void
     * @throws Exception if the selectedResources can't be used to buy the tile
     */
    public function selectLeader(PlayerGLM $playerGLM) : void
    {
        $personalBoard = $playerGLM->getPersonalBoard();
        $selectedTile = $personalBoard->getBuyingTile();
        $resource = $this->resourceGLMRepository->findOneBy([
            'type' => GlenmoreParameters::$VILLAGER_RESOURCE
        ]);
        $this->addSelectedResourcesFromTileWithCost($playerGLM, null, $resource,
            $selectedTile->getBoardTile()->getTile()->getBuyPrice());
    }


    /**
     * selectResourcesFromTileToActivate: select a resources from a tile to use to activate the selectedTile of the
     * player
     * @param PlayerTileGLM $playerTileGLM
     * @param ResourceGLM $resource
     * @return void
     * @throws \Exception if the selectedResources can't be used to activate the tile
     */
    public function selectResourcesFromTileToActivate(PlayerTileGLM $playerTileGLM, ResourceGLM $resource) : void
    {
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $selectedTile = $personalBoard->getActivatedTile();
        $this->addSelectedResourcesFromTileWithCost($personalBoard->getPlayerGLM(), $playerTileGLM, $resource,
            $selectedTile->getTile()->getActivationPrice());
    }


    /**
     * buyTile: remove the selected resources from the player to buy the tile
     * @param TileGLM $tile
     * @param PlayerGLM $player
     * @return void
     * @throws \Exception if invalid amount of selected resources
     */
    public function buyTile(TileGLM $tile, PlayerGLM $player) : void
    {
        if ($tile->getName() === GlenmoreParameters::$CARD_LOCH_NESS) {
            if ($player->getPersonalBoard()->getSelectedResources()->first()->getPlayerTile() == null) {
                $leaderCount = $player->getPersonalBoard()->getLeaderCount();
                $player->getPersonalBoard()->setLeaderCount($leaderCount - 1);
                $this->entityManager->persist($player->getPersonalBoard());
                $this->entityManager->flush();
                return;
            }
        }
        if ($tile->getName() === GlenmoreParameters::$TILE_NAME_TAVERN) {
            if (!$this->buyTavern($player)) {
                throw new Exception("not enough whisky");
            }
            return;
        }
        if ($tile->getName() === GlenmoreParameters::$CARD_LOCH_OICH) {
            $this->buyLochOich($player);
            return;
        }
        if(!$this->canBuyTileWithSelectedResources($player, $tile)) {
            throw new Exception("can't buy tile with selected resources, wrong selected resources");
        }
        $globalResources = $player->getPersonalBoard()->getSelectedResources();
        foreach ($tile->getBuyPrice() as $buyPrice) {
            $resourceTile = $buyPrice->getResource();
            $priceTile = $buyPrice->getPrice();
            $resourcesOfPlayerLikeResourceTile = $globalResources->filter(
                function (SelectedResourceGLM $selectedResourceGLM) use ($resourceTile) {
                    return $selectedResourceGLM->getResource()->getId() == $resourceTile->getId();
                }
            );
            foreach ($resourcesOfPlayerLikeResourceTile as $resource) {
                $playerTile = $resource->getPlayerTile();
                if ($resource->getQuantity() > $priceTile) {
                    $resource->setQuantity($resource->getQuantity() - $priceTile);
                    $playerTileResource = $playerTile->getPlayerTileResource()->filter(
                        function (PlayerTileResourceGLM $playerTileResourceGLM) use ($resource) {
                            return $playerTileResourceGLM->getResource()->getId() == $resource->getResource()->getId();
                    })->first();
                    $playerTileResource->setQuantity($resource->getQuantity() - $priceTile);
                    $this->entityManager->persist($playerTileResource);
                    $priceTile = 0;
                } else {
                    $player->getPersonalBoard()->removeSelectedResource($resource);
                    if ($playerTile != null ) {
                        $playerTileResource = $playerTile->getPlayerTileResource()->filter(
                            function(PlayerTileResourceGLM $playerTileResourceGLM) use ($resource) {
                                return $playerTileResourceGLM->getResource()->getId() == $resource->getResource()->getId();
                            })->first();
                        $playerTileResource->setQuantity($playerTileResource->getQuantity() - $priceTile);
                        $this->entityManager->persist($playerTileResource);
                    }
                    $priceTile -= $resource->getQuantity();
                    $this->entityManager->remove($resource);
                }
                if ($priceTile == 0) {
                    break;
                }
            }
        }
        $this->clearResourceSelection($player);
        $this->entityManager->flush();
    }

    /**
     * Place a new tile from draw tiles on main board
     * @param PlayerGLM $player
     * @param DrawTilesGLM $drawTiles
     * @return void
     */
    public function placeNewTile(PlayerGLM $player, DrawTilesGLM $drawTiles) : void
    {
        // Initialization

        $mainBoard = $player->getGameGLM()->getMainBoard();
        $currentPosition = $player->getPawn()->getPosition();
        $posTile = $currentPosition;
        $posTile -= 1;
        // Search last position busy by a tile
        while ($this->getBoardTilesAtPosition($mainBoard, $posTile) == null
            && $this->getPawnsAtPosition($player, $mainBoard, $posTile) == null)
        {
            $posTile -= 1;
            if ($posTile < 0) {
                $posTile += GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
            }
        }

        // Search next position relative to the position found
        $posTile += 1;
        if ($posTile == GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD) {
            $posTile = 0;
        }

        // Manage draw tile and update
        $tile = $drawTiles->getTiles()->last();
        $drawTiles->removeTile($tile);
        $this->entityManager->persist($drawTiles);

        // Manage main board and update
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setPosition($posTile);
        $boardTile->setMainBoardGLM($mainBoard);
        $mainBoard->addBoardTile($boardTile);
        $this->entityManager->persist($boardTile);
        $this->entityManager->persist($mainBoard);

        $this->entityManager->flush();
    }

    /**
     * chooseTileToActivate : set activated tile to tile
     * @param PlayerTileGLM $tileGLM
     * @return void
     */
    public function chooseTileToActivate(PlayerTileGLM $tileGLM) : void
    {
        $personalBoard = $tileGLM->getPersonalBoard();
        $personalBoard->setActivatedTile($tileGLM);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * moveVillager : moves a villager, placed on the tile, towards the direction
     *
     * @param PlayerTileGLM $playerTileGLM
     * @param int           $direction
     * @return PlayerTileGLM
     * @throws Exception
     */
    public function moveVillager(PlayerTileGLM $playerTileGLM, int $direction) : PlayerTileGLM
    {
        $targetedTile = $this->getTargetedTile($playerTileGLM, $direction);
        if($targetedTile == null) {
            throw new Exception("no targeted tile in this direction");
        }

        if (!$this->doesTileContainsVillager($playerTileGLM)) {
            throw new Exception("no villager placed on this tile");
        }
        $player = $playerTileGLM->getPersonalBoard()->getPlayerGLM();

        $movementPoint = $this->getMovementPoints($playerTileGLM->getPersonalBoard()->getPlayerGLM());
        if ($movementPoint <= 0) {
            throw new Exception("no more movement points");
        }
        // removes villager from the tile
        $villager = $this->retrieveVillagerFromTile($playerTileGLM);
        // places villager onto wanted tile
        $this->placeVillagerOntoTile($villager, $targetedTile);
        // removes a movement point from the player
        $this->lowerMovementPoints($player);
        return $targetedTile;
    }

    /**
     * removeVillager : removes a villager from a tile, it becomes a leader
     *
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     * @throws Exception
     */
    public function removeVillager(PlayerTileGLM $playerTileGLM) : void
    {
        if (!$this->doesTileContainsVillager($playerTileGLM)) {
            throw new Exception("no villager placed on this tile");
        }
        $personalBoard = $playerTileGLM->getPersonalBoard();
        if ($this->getVillagerCount($personalBoard) <= GlenmoreParameters::$MIN_VILLAGER_PER_VILLAGE) {
            throw new Exception("not enough villager on player's village");
        }
        $player = $playerTileGLM->getPersonalBoard()->getPlayerGLM();
        $movementPoint = $this->getMovementPoints($playerTileGLM->getPersonalBoard()->getPlayerGLM());
        if ($movementPoint == 0) {
            throw new Exception("no more movement points");
        }
        // removes villager from the tile
        $this->retrieveVillagerFromTile($playerTileGLM);
        $personalBoard = $player->getPersonalBoard();
        $personalBoard->setLeaderCount($personalBoard->getLeaderCount() + 1);
        $this->entityManager->persist($personalBoard);
        // removes a movement point from the player
        $this->lowerMovementPoints($player);
    }

    /**
     * verifyAllPositionOnPersonalBoard : for each player tile, verify if the tile in parameter can be placed next to it
     * @param PersonalBoardGLM $personalBoardGLM
     * @param TileGLM $tileGLM
     * @return array
     */
    public function verifyAllPositionOnPersonalBoard(PersonalBoardGLM $personalBoardGLM, TileGLM $tileGLM): array
    {
        $acceptableCoords = [];
        foreach($personalBoardGLM->getPlayerTiles() as $playerTile) {
            $x = $playerTile->getCoordX();
            $y = $playerTile->getCoordY();
            if($this->canPlaceTile($x - 1, $y, $tileGLM, $personalBoardGLM->getPlayerGLM())) {
                $acceptableCoords[] = [$x - 1, $y];
            }
            if($this->canPlaceTile($x + 1, $y, $tileGLM, $personalBoardGLM->getPlayerGLM())) {
                $acceptableCoords[] = [$x + 1, $y];
            }
            if($this->canPlaceTile($x, $y - 1, $tileGLM, $personalBoardGLM->getPlayerGLM())) {
                $acceptableCoords[] = [$x, $y - 1];
            }
            if($this->canPlaceTile($x, $y + 1, $tileGLM, $personalBoardGLM->getPlayerGLM())) {
                $acceptableCoords[] = [$x, $y + 1];
            }
        }
        return $acceptableCoords;
    }

    /**
     * Assign tile for player when conditions are verified
     * @param BoardTileGLM $boardTile
     * @param PlayerGLM $player
     * @throws Exception
     */
    public function assignTileToPlayer(BoardTileGLM $boardTile, PlayerGLM $player) : array
    {
        if ($boardTile->getTile()->getName() === GlenmoreParameters::$CARD_LOCH_OICH) {
            if (!$this->canBuyLochOich($boardTile->getTile(), $player)) {
                throw new Exception("Unable to buy tile");
            }
        } else {
            if (!$this->canBuyTile($boardTile->getTile(), $player)) {
                throw new Exception("Unable to buy tile");
            }
        }
        if ($player->getPersonalBoard()->getBuyingTile() != null) {
            throw new Exception("Already bought a tile");
        }
        $possiblePlacement = $this->verifyAllPositionOnPersonalBoard($player->getPersonalBoard(), $boardTile->getTile());
        if (count($possiblePlacement) == 0) {
            throw new Exception("There is no place for this tile");
        }
        // Initialization
        $personalBoard = $player->getPersonalBoard();
        // Manage personal board and update
        $buyingTile = new BuyingTileGLM();
        $buyingTile->setBoardTile($boardTile);
        $personalBoard->setBuyingTile($buyingTile);
        $buyingTile->setPersonalBoardGLM($personalBoard);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->persist($boardTile);
        $this->entityManager->persist($buyingTile);
        $this->entityManager->flush();
        return $possiblePlacement;
    }

    /**
     * clearTileSelection : cancel the tile that a player has selected to buy it
     * @param PlayerGLM $player
     * @return void
     */
    public function clearTileSelection(PlayerGLM $player): void
    {
        $buyingTile = $player->getPersonalBoard()->getBuyingTile();
        $player->getPersonalBoard()->setBuyingTile(null);
        $this->entityManager->remove($buyingTile);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
    }

    /**
     * clearTileActivationSelection : cancel the tile that a player has selected to activate it
     * @param PlayerGLM $player
     * @return void
     */
    public function clearTileActivationSelection(PlayerGLM $player): void
    {
        $player->getPersonalBoard()->setActivatedTile(null);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
    }

    /**
     * Place a selected tile in personal board's player at position (abscissa, ordinate)
     *
     * @param PlayerGLM $player
     * @param int       $abscissa
     * @param int       $ordinate
     * @return void
     * @throws Exception
     * @throws \Exception
     */
    public function setPlaceTileAlreadySelected(PlayerGLM $player, int $abscissa, int $ordinate) : void
    {
        if ($player->getPersonalBoard()->getBuyingTile() == null)
        {
            throw new Exception("no tile selected");
        }

        // Initialization
        $personalBoard = $player->getPersonalBoard();
        $mainBoard = $player->getGameGLM()->getMainBoard();
        $tileSelected = $personalBoard->getBuyingTile()->getBoardTile();
        $lastPosition = $player->getPawn()->getPosition();
        $newPosition = $tileSelected->getPosition();

        // Check if condition for assign tile
        if (!$this->canPlaceTile($abscissa, $ordinate, $tileSelected->getTile(), $player))
        {
            throw new Exception("Unable to place tile");
        }
        try {
            $this->buyTile($tileSelected->getTile(), $player);
        } catch(Exception $e) {
            $personalBoard->getBuyingTile()->setCoordX($abscissa);
            $personalBoard->getBuyingTile()->setCoordY($ordinate);
            $this->entityManager->persist($personalBoard->getBuyingTile());
            $this->entityManager->flush();
            throw new \Exception('Invalid amount of selected resources' . $e->getMessage());
        }

        // Here assign tile --> Creation of player tile and manage personal board
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tileSelected->getTile());
        $playerTile->setPersonalBoard($personalBoard);
        $playerTile->setCoordX($abscissa);
        $playerTile->setCoordY($ordinate);
        $this->entityManager->persist($playerTile);
        $this->manageAdjacentTiles($abscissa, $ordinate, $player, $playerTile);
        $personalBoard->addPlayerTile($playerTile);
        // Manage main board
        $mainBoard->removeBoardTile($tileSelected);
        $mainBoard->setLastPosition($lastPosition);
        // Set new position of pawn's player
        $player->getPawn()->setPosition($newPosition);
        // Update
        $this->entityManager->remove($tileSelected);
        $buyingTile = $personalBoard->getBuyingTile();
        $this->entityManager->persist($personalBoard);
        $personalBoard->setBuyingTile(null);
        $this->entityManager->remove($buyingTile);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($player->getPawn());
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * retrieveVillagerFromTile : returns one villager from the tile and removes it
     *
     * @param PlayerTileGLM $playerTileGLM
     * @return ResourceGLM|null
     */
    private function retrieveVillagerFromTile(PlayerTileGLM $playerTileGLM) : ?ResourceGLM
    {
        $tileResources = $playerTileGLM->getPlayerTileResource();
        foreach ($tileResources as $tileResource) {
            if ($tileResource->getResource()->getType() === GlenmoreParameters::$VILLAGER_RESOURCE) {
                $villager = $tileResource->getResource();
                $tileResource->setQuantity($tileResource->getQuantity() - 1);
                $this->entityManager->persist($tileResource);
                $this->entityManager->flush();
                return $villager;
            }
        }
        return null;
    }

    /**
     * hasPlayerEnoughResourcesToActivate : checks if player has enough resources to activate
     *      the tile
     * @param PlayerTileGLM $playerTileGLM
     * @param PlayerGLM $playerGLM
     * @return int
     */
    private function hasPlayerEnoughResourcesToActivate(PlayerTileGLM $playerTileGLM, PlayerGLM $playerGLM): int
    {
        $result = -1;
        if (!$this->hasActivationCost($playerTileGLM)) {
            return 0;
        }
        $tileGLM = $playerTileGLM->getTile();
        $activationPrices = $tileGLM->getActivationPrice();
        $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
        if ($playerTileGLM->getTile()->getName() === GlenmoreParameters::$TILE_NAME_FAIR) {
            if ($selectedResources->count() >= 1) {
                return 0;
            }
            return -1;
        }
        if ($playerTileGLM->getTile()->getName() === GlenmoreParameters::$TILE_NAME_GROCER) {
            if ($selectedResources->count() >= 3) {
                return 0;
            }
            return -1;
        }
        foreach ($activationPrices as $activationPrice){
            $resourceType = $activationPrice->getResource();
            $resourceAmount = $activationPrice->getPrice();
            $playerResourceCount = 0;
            if($resourceType->getType() == GlenmoreParameters::$PRODUCTION_RESOURCE){
                foreach ($selectedResources as $selectedResource){
                    if($selectedResource->getResource()->getColor() == $resourceType->getColor()){
                        $playerResourceCount += $selectedResource->getQuantity();
                    }
                }
            }
            if($playerResourceCount >= $resourceAmount){
                $result += 1;
            }
        }
        return $result;
    }

    /**
     * buyTavern : tries to buy a tavern
     * @param PlayerGLM $playerGLM
     * @return bool
     */
    private function buyTavern(PlayerGLM $playerGLM) : bool
    {
        $personalBoard = $playerGLM->getPersonalBoard();
        $playerTiles = $personalBoard->getPlayerTiles();
        foreach ($playerTiles as $playerTile) {
            $resources = $playerTile->getPlayerTileResource();
            foreach ($resources as $resource) {
                if ($resource->getResource()->getType() == GlenmoreParameters::$WHISKY_RESOURCE) {
                    $resource->setQuantity($resource->getQuantity() - 1);
                    $this->entityManager->persist($resource);
                    $this->entityManager->flush();
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * hasEnoughPlaceToActivate : checks if tiles can contain new resources
     * @param PlayerTileGLM $playerTileGLM
     * @return bool
     */
    private function hasEnoughPlaceToActivate(PlayerTileGLM $playerTileGLM): bool
    {
        $tileGLM = $playerTileGLM->getTile();
        $bonusResources = $tileGLM->getActivationBonus();
        $bonusCount = 0;
        foreach ($bonusResources as $bonusResource){
            if($bonusResource->getResource()->getType() == GlenmoreParameters::$PRODUCTION_RESOURCE){
                $bonusCount += $bonusResource->getAmount();
            }
        }
        $playerResourceCount = 0;
        $resourcesOnTile = $playerTileGLM->getPlayerTileResource();
        foreach ($resourcesOnTile as $resourceOnTile){
            if($resourceOnTile->getResource()->getType() == GlenmoreParameters::$PRODUCTION_RESOURCE) {
                $playerResourceCount += $resourceOnTile->getQuantity();
            }
        }
        $totalCount = $bonusCount + $playerResourceCount;
        return $totalCount <= GlenmoreParameters::$MAX_RESOURCES_PER_TILE;
    }

    /**
     * verifyRoadAndRiverConstraints: return true if the player can place the tile with the selected adjacentTiles
     *                                regarding rivers and roads
     *
     * @param TileGLM $tile
     * @param ?PlayerTileGLM $tileLeft
     * @param ?PlayerTileGLM $tileRight
     * @param ?PlayerTileGLM $tileUp
     * @param ?PlayerTileGLM $tileDown
     * @return bool
     */
    private function verifyRoadAndRiverConstraints(TileGLM $tile, ?PlayerTileGLM $tileLeft, ?PlayerTileGLM $tileRight,
                                                   ?PlayerTileGLM $tileUp, ?PlayerTileGLM $tileDown) : bool
    {
        $canPlace = true;
        if ($tile->isContainingRiver()) {
            $canPlace = ($tileUp != null && $tileUp->getTile()->isContainingRiver())
                || ($tileDown != null && $tileDown->getTile()->isContainingRiver());
        }
        if ($tile->isContainingRoad()) {
            $canPlace = $canPlace && ($tileLeft != null && $tileLeft->getTile()->isContainingRoad())
                || ($tileRight != null && $tileRight->getTile()->isContainingRoad());
        }
        if (!$tile->isContainingRiver()) {
            $canPlace = $canPlace && ($tileUp == null || !$tileUp->getTile()->isContainingRiver())
                && ($tileDown == null || !$tileDown->getTile()->isContainingRiver());
        }
        if (!$tile->isContainingRoad()) {
            $canPlace = $canPlace && ($tileLeft == null || !$tileLeft->getTile()->isContainingRoad())
                && ($tileRight == null || !$tileRight->getTile()->isContainingRoad());
        }
        if(!$tile->isContainingRiver() && !$tile->isContainingRoad()) {
            $canPlace = $canPlace && ($tileLeft != null || $tileRight != null || $tileUp != null || $tileDown != null);
        }
        return $canPlace;
    }

    /**
     * doesTileContainVillager: return true if the tile contains a villager
     * @param PlayerTileGLM $playerTile
     * @return bool
     */
    private function doesTileContainsVillager(PlayerTileGLM $playerTile) : bool
    {
        $villager = $this->resourceGLMRepository->findOneBy(['type' => GlenmoreParameters::$VILLAGER_RESOURCE]);
        $villager = $this->playerTileResourceGLMRepository->findOneBy(['resource' => $villager->getId(),
                'playerTileGLM' => $playerTile->getId()]);
        return $villager != null && $villager->getQuantity() > 0;
    }

    /**
     * @param PersonalBoardGLM $personalBoardGLM
     * @return int
     */
    private function getVillagerCount(PersonalBoardGLM $personalBoardGLM) : int
    {
        $player = $personalBoardGLM->getPlayerGLM();
        $villager = $this->resourceGLMRepository->findOneBy(['type' => GlenmoreParameters::$VILLAGER_RESOURCE]);
        $villagers =  $this->playerTileResourceGLMRepository->findBy(['resource' => $villager->getId(),
                'player' => $player->getId()]);
        $result = 0;
        foreach ($villagers as $villager) {
            $result += $villager->getQuantity();
        }
        return $result;
    }

    /**
     * isVillagerInAdjacentTiles: return true if a villager is found in at least one of the adjacentTiles
     * @param Collection $adjacentTiles
     * @return bool
     */
    private function isVillagerInAdjacentTiles(Collection $adjacentTiles) : bool
    {
        return !$adjacentTiles->filter(function(?PlayerTileGLM $tile) {
            return $tile != null && $this->doesTileContainsVillager($tile);
        })->isEmpty();
    }

    /**
     * getActivePlayer : return the player who needs to play
     * @param GameGLM $gameGLM
     * @return PlayerGLM
     */
    public function getActivePlayer(GameGLM $gameGLM): PlayerGLM
    {
        return $this->playerGLMRepository->findOneBy(["gameGLM" => $gameGLM->getId(),
            "turnOfPlayer" => true]);
    }

    /**
     * Return a board tile that position is equal to parameter else null
     * @param MainBoardGLM $mainBoard
     * @param int $position
     * @return BoardTileGLM|null
     */
    public function getBoardTilesAtPosition(MainBoardGLM $mainBoard, int $position): BoardTileGLM|null
    {
        $boardTiles = $mainBoard->getBoardTiles();
        foreach ($boardTiles as $boardTile) {
            if ($boardTile->getPosition() == $position)
            {
               return $boardTile;
            }
        }
        return null;
    }

    /**
     * Return a pawn that position is equal to parameter else null
     * @param PlayerGLM $playerGLM
     * @param MainBoardGLM $mainBoard
     * @param int $position
     * @return BoardTileGLM|null
     */
    private function getPawnsAtPosition(PlayerGLM $playerGLM, MainBoardGLM $mainBoard, int $position): PawnGLM|null
    {
       $pawns = new ArrayCollection();
       foreach ($mainBoard->getPawns() as $pawn) {
           //if ($pawn->getPlayerGLM() !== $playerGLM) {
               $pawns->add($pawn);
           //}
       }
        foreach ($pawns as $pawn) {
            if ($pawn->getPosition() == $position)
            {
                return $pawn;
            }
        }
        return null;
    }


    /**
     * getTileInPosition : return BoardTileGLM in position $position,
     *      or null if there is no tile here
     * @param MainBoardGLM $mainBoardGLM
     * @param $position
     * @return BoardTileGLM|null
     */
    private function getTileInPosition(MainBoardGLM $mainBoardGLM, $position): BoardTileGLM | null
    {
        $mainBoardTiles = $mainBoardGLM->getBoardTiles();
        foreach ($mainBoardTiles as $boardTile){
            if($boardTile->getPosition() == $position){
                return $boardTile;
            }
        }
        return null;
    }

    /**
     * addSelectedResourcesFromTileWithCost: create a selected resources from the playerTile with the selected resource,
     *                                       if it's coherent with the cost
     * @param PlayerGLM $playerGLM
     * @param PlayerTileGLM|null $playerTileGLM
     * @param ResourceGLM $resource
     * @param Collection<TileBuyCostGLM> $cost cost of the tile
     * @return void
     * @throws Exception if the selected resources can't be used to buy the tile
     */
    public function addSelectedResourcesFromTileWithCost(PlayerGLM $playerGLM, ?PlayerTileGLM $playerTileGLM,
                                                         ResourceGLM $resource, Collection $cost) : void
    {
        if ($playerGLM->getRoundPhase() == GlenmoreParameters::$ACTIVATION_PHASE &&
            $playerGLM->getPersonalBoard()->getActivatedTile()->getTile()->getName()
                === GlenmoreParameters::$TILE_NAME_FAIR) {
            $this->selectResourceForFair($playerGLM, $playerTileGLM, $resource, $cost);
            return;
        }
        if ($playerGLM->getRoundPhase() == GlenmoreParameters::$ACTIVATION_PHASE &&
            $playerGLM->getPersonalBoard()->getActivatedTile()->getTile()->getName()
            === GlenmoreParameters::$TILE_NAME_GROCER) {
            $this->selectResourceForGrocer($playerGLM, $playerTileGLM, $resource, $cost);
            return;
        }
        if ($playerGLM->getRoundPhase() == GlenmoreParameters::$BUYING_PHASE &&
            $playerGLM->getPersonalBoard()->getBuyingTile()->getBoardTile()->getTile()->getName()
            === GlenmoreParameters::$CARD_LOCH_OICH) {
            $this->selectResourceForLochOich($playerGLM, $playerTileGLM, $resource, $cost);
            return;
        }
        $personalBoard = $playerGLM->getPersonalBoard();
        $selectedResources = $personalBoard->getSelectedResources();
        $selectedResourcesLikeResource = $selectedResources->filter(
            function (SelectedResourceGLM $selectedResourceGLM) use ($resource) {
                return $selectedResourceGLM->getResource()->getId() == $resource->getId();
            });
        $numberOfSelectedResources = 0;
        foreach ($selectedResourcesLikeResource as $selectedResourceLikeResource) {
            $numberOfSelectedResources += $selectedResourceLikeResource->getQuantity();
        }
        $player = $personalBoard->getPlayerGLM();
        if ($player->getRoundPhase() == GlenmoreParameters::$BUYING_PHASE) {
            $priceCost = $cost->filter(
                function(TileBuyCostGLM $buyCost) use ($resource) {
                    return $buyCost->getResource()->getId() == $resource->getId();
                })->first();
            $priceCost = !$priceCost ? 0 : $priceCost->getPrice();
            if ($numberOfSelectedResources >= $priceCost) {
                throw new \Exception('Impossible to choose this resource');
            }
        } else if ($player->getRoundPhase() == GlenmoreParameters::$ACTIVATION_PHASE) {
            $activationCost = $cost->filter(
                function(TileActivationCostGLM $activationCost) use ($resource) {
                    return $activationCost->getResource()->getId() == $resource->getId();
                })->first();
        }

        $selectedResourceWithSamePlayerTile = null;
        if ($playerTileGLM != null ) {
            $selectedResourceWithSamePlayerTile = $this->selectedResourceGLMRepository
                ->findOneBy(["playerTile" => $playerTileGLM->getId(), "resource" => $resource->getId()]);
        }
        if ($selectedResourceWithSamePlayerTile == null && $playerTileGLM != null) {
            $selectedResource = new SelectedResourceGLM();
            $selectedResource->setPlayerTile($playerTileGLM);
            $selectedResource->setResource($resource);
            $selectedResource->setQuantity(1);
            $selectedResource->setPersonalBoardGLM($playerTileGLM->getPersonalBoard());
            $this->entityManager->persist($selectedResource);
            $personalBoard->addSelectedResource($selectedResource);
            $this->entityManager->persist($personalBoard);
        } else if ($selectedResourceWithSamePlayerTile != null) {
            $selectedResourceWithSamePlayerTile
                ->setQuantity($selectedResourceWithSamePlayerTile->getQuantity() + 1);
            $this->entityManager->persist($selectedResourceWithSamePlayerTile);
        } else {
            $selectedResource = new SelectedResourceGLM();
            $selectedResource->setPlayerTile(null);
            $selectedResource->setResource($resource);
            $selectedResource->setQuantity(1);
            $selectedResource->setPersonalBoardGLM($playerGLM->getPersonalBoard());
            $this->entityManager->persist($selectedResource);
            $personalBoard->addSelectedResource($selectedResource);
            $this->entityManager->persist($personalBoard);
            $this->entityManager->persist($selectedResource);
        }

        $this->entityManager->flush();
    }

    /**
     * clearResourceSelection : clear the collection of selected resources of the player
     * @param PlayerGLM $playerGLM
     * @return void
     */
    public function clearResourceSelection(PlayerGLM $playerGLM) : void
    {
        $playerGLM->getPersonalBoard()->getSelectedResources()->clear();
        $this->entityManager->persist($playerGLM->getPersonalBoard());
        $this->entityManager->flush();
    }

    /**
     * removeTilesOfBrokenChain : removes tiles of $mainBoardGLM
     *      when chain is broken
     * @param MainBoardGLM $mainBoardGLM
     * @return void
     */
    private function removeTilesOfBrokenChain(MainBoardGLM $mainBoardGLM): void
    {
        $player = $this->getActivePlayer($mainBoardGLM->getGameGLM());
        $playerPosition = $player->getPawn()->getPosition();
        $pointerPosition = $playerPosition - 1;
        if($pointerPosition == -1){
            $pointerPosition = GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1;
        }
        while (($tile = $this->getTileInPosition($mainBoardGLM, $pointerPosition)) != null){
            $mainBoardGLM->removeBoardTile($tile);
            $pointerPosition -= 1;
            if($pointerPosition == -1){
                $pointerPosition = GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1;
            }
            $this->entityManager->persist($mainBoardGLM);
            $this->entityManager->remove($tile);
        }
        $this->entityManager->flush();
    }

    /**
     * givePlayerActivationBonus : gives to player his activation bonus
     * @param PlayerTileGLM $playerTileGLM
     * @param PlayerGLM $playerGLM
     * @return void
     * @throws \Exception
     */
    private function givePlayerActivationBonus(PlayerTileGLM $playerTileGLM, PlayerGLM $playerGLM): void
    {
        $tileGLM = $playerTileGLM->getTile();
        $activationBonusResources = $tileGLM->getActivationBonus();
        foreach ($activationBonusResources as $activationBonusResource){
            $exists = false;
            foreach ($playerTileGLM->getPlayerTileResource() as $tileResource) {
                if ($tileResource->getResource() === $activationBonusResource->getResource()) {
                    $tileResource->setQuantity($tileResource->getQuantity() + 1);
                    $this->entityManager->persist($tileResource);
                    $exists = true;
                    $playerTileGLM->setActivated(true);
                    $this->entityManager->persist($playerTileGLM);
                }
            }
            if (!$exists) {
                $playerTileResource = new PlayerTileResourceGLM();
                $playerTileResource->setPlayer($playerTileGLM->getPersonalBoard()->getPlayerGLM());
                $playerTileResource->setPlayerTileGLM($playerTileGLM);
                $playerTileResource->setResource($activationBonusResource->getResource());
                $playerTileResource->setQuantity(1);
                $this->entityManager->persist($playerTileResource);
                $playerTileGLM->addPlayerTileResource($playerTileResource);
                $playerTileGLM->setActivated(true);
                $this->entityManager->persist($playerTileGLM);
            }
        }
        $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
        $activationPrices = $playerTileGLM->getTile()->getActivationPrice();
        foreach ($activationPrices as $activationPrice){
            $activationCost = $activationPrice->getPrice();
            foreach ($selectedResources as $selectedResource) {
                if($selectedResource->getResource()->getColor() == $activationPrice->getResource()->getColor()
                    && $activationCost > 0){
                    while ($selectedResource->getQuantity() > 0 && $activationCost > 0){
                        $activationCost -= 1;
                        $selectedResource->setQuantity($selectedResource->getQuantity() - 1);
                        $this->entityManager->persist($selectedResource);
                    }
                    $this->entityManager->persist($playerGLM->getPersonalBoard());
                }
            }
            if($activationCost > 0){
                throw new \Exception("Error : Not enough resources to activate");
            }
        }
        $this->entityManager->flush();
    }

    /**
     * giveAllMovementPoints : for each tile having a movement activation bonus, gives the resource
     *  to the player;
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function giveAllMovementPoints(PlayerTileGLM $playerTileGLM) : void
    {
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $tiles = new ArrayCollection();
        $tiles->add($playerTileGLM);
        foreach ($playerTileGLM->getAdjacentTiles() as $adjacentTile) {
            $tiles->add($adjacentTile);
        }
        foreach ($tiles as $adjacentTile) {
            if ($adjacentTile->getTile()->getType() === GlenmoreParameters::$TILE_TYPE_CASTLE
                || $adjacentTile->getTile()->getType() === GlenmoreParameters::$TILE_TYPE_VILLAGE) {
                $resource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$MOVEMENT_RESOURCE]);
                $exists = false;
                foreach ($adjacentTile->getPlayerTileResource() as $tileResource) {
                    if ($tileResource->getResource()->getType() === GlenmoreParameters::$MOVEMENT_RESOURCE) {
                        $tileResource->setQuantity(1);
                        $this->entityManager->persist($tileResource);
                        $exists = true;
                        $adjacentTile->setActivated(true);
                        $this->entityManager->persist($adjacentTile);
                    }
                }
                if (!$exists) {
                    $playerTileResource = new PlayerTileResourceGLM();
                    $playerTileResource->setPlayer($playerTileGLM->getPersonalBoard()->getPlayerGLM());
                    $playerTileResource->setPlayerTileGLM($adjacentTile);
                    $playerTileResource->setResource($resource);
                    $playerTileResource->setQuantity(1);
                    $this->entityManager->persist($playerTileResource);
                    $adjacentTile->addPlayerTileResource($playerTileResource);
                    $adjacentTile->setActivated(true);
                    $this->entityManager->persist($adjacentTile);
                }
            }
        }
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * placeVillagerOntoNewTile : places the resource villager onto the tile
     * @param ResourceGLM   $villager
     * @param PlayerTileGLM $newTile
     * @return void
     */
    private function placeVillagerOntoTile(ResourceGLM $villager, PlayerTileGLM $newTile) : void
    {
        $player = $newTile->getPersonalBoard()->getPlayerGLM();
        $tileResources = $newTile->getPlayerTileResource();
        $found = false;
        foreach ($tileResources as $tileResource) {
            if ($tileResource->getResource() === $villager) {
                $tileResource->setQuantity($tileResource->getQuantity() + 1);
                $this->entityManager->persist($tileResource);
                $this->entityManager->persist($newTile);
                $found = true;
                break;
            }
        }
        if (!$found) {
            $tileResource = new PlayerTileResourceGLM();
            $tileResource->setResource($villager);
            $tileResource->setPlayerTileGLM($newTile);
            $tileResource->setPlayer($player);
            $tileResource->setQuantity(1);
            $this->entityManager->persist($tileResource);
            $newTile->addPlayerTileResource($tileResource);
            $this->entityManager->persist($newTile);
        }
        $this->entityManager->flush();
    }

    /**
     * lowerMovementPoints : removes one movement point from the player
     * @param PlayerGLM|null $player
     * @return void
     */
    private function lowerMovementPoints(?PlayerGLM $player) : void
    {
        $personalBoard = $player->getPersonalBoard();
        $tiles = $personalBoard->getPlayerTiles();
        foreach ($tiles as $playerTile) {
            foreach ($playerTile->getPlayerTileResource() as $tileResource) {
               if ($tileResource->getResource()->getType() === GlenmoreParameters::$MOVEMENT_RESOURCE) {
                   $tileResource->setQuantity($tileResource->getQuantity() - 1);
                   $this->entityManager->persist($tileResource);
                   $this->entityManager->flush();
                   return;
               }
            }
        }
    }

    /**
     * getTargetedTile : return the tile targeted with the direction passed
     * @param PlayerTileGLM $playerTileGLM
     * @param int $direction
     * @return ?PlayerTileGLM
     * @throws Exception
     */
    private function getTargetedTile(PlayerTileGLM $playerTileGLM, int $direction) : ?PlayerTileGLM
    {
        $tileX = $playerTileGLM->getCoordX();
        $tileY = $playerTileGLM->getCoordY();
        $personalBoard = $playerTileGLM->getPersonalBoard();
        switch ($direction) {
            case GlenmoreParameters::$NORTH :
                return $this->playerTileGLMRepository->findOneBy(['coord_X' => $tileX - 1, 'coord_Y' => $tileY, 'personalBoard' => $personalBoard->getId()]);
            case GlenmoreParameters::$SOUTH :
                return $this->playerTileGLMRepository->findOneBy(['coord_X' => $tileX + 1, 'coord_Y' => $tileY, 'personalBoard' => $personalBoard->getId()]);
            case GlenmoreParameters::$EAST :
                return $this->playerTileGLMRepository->findOneBy(['coord_X' => $tileX, 'coord_Y' => $tileY + 1, 'personalBoard' => $personalBoard->getId()]);
            case GlenmoreParameters::$WEST :
                return $this->playerTileGLMRepository->findOneBy(['coord_X' => $tileX, 'coord_Y' => $tileY - 1, 'personalBoard' => $personalBoard->getId()]);
            case GlenmoreParameters::$NORTH_WEST :
                return $this->playerTileGLMRepository->findOneBy(['coord_X' => $tileX - 1, 'coord_Y' => $tileY - 1, 'personalBoard' => $personalBoard->getId()]);
            case GlenmoreParameters::$NORTH_EAST :
                return $this->playerTileGLMRepository->findOneBy(['coord_X' => $tileX - 1, 'coord_Y' => $tileY + 1, 'personalBoard' => $personalBoard->getId()]);
            case GlenmoreParameters::$SOUTH_WEST :
                return $this->playerTileGLMRepository->findOneBy(['coord_X' => $tileX + 1, 'coord_Y' => $tileY - 1, 'personalBoard' => $personalBoard->getId()]);
            case GlenmoreParameters::$SOUTH_EAST :
                return $this->playerTileGLMRepository->findOneBy(['coord_X' => $tileX + 1, 'coord_Y' => $tileY + 1, 'personalBoard' => $personalBoard->getId()]);
            default:
                throw new Exception('Unexpected value');
        }
    }

    /**
     * canPickThisResource : checks if resource is different from all other resources contained in selectedResources
     * @param Collection  $selectedResources
     * @param ResourceGLM $resource
     * @return bool
     */
    private function canPickThisResource(Collection $selectedResources, ResourceGLM $resource) : bool
    {
        foreach ($selectedResources as $selectedResource) {
            if ($selectedResource->getResource()->getType() == $resource->getType() &&
                $selectedResource->getResource()->getColor() == $resource->getColor()) {
                return false;
            }
        }
        return true;
    }

    /**
     * getTilesAround : returns every PlayerTile around coordinates
     * @param int $x
     * @param int $y
     * @param PlayerGLM $player
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function manageAdjacentTiles(int $x, int $y, PlayerGLM $player, PlayerTileGLM $playerTileGLM) : void
    {
        $result = new ArrayCollection();
        $tileLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        if ($tileLeft != null) {
            $result->set(GlenmoreParameters::$WEST, $tileLeft);
        }
        $tileRight = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x, 'coord_Y' => $y + 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        if ($tileRight != null) {
            $result->set(GlenmoreParameters::$EAST, $tileRight);
        }
        $tileUp = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y
            , 'personalBoard' => $player->getPersonalBoard()]);
        if ($tileUp != null) {
            $result->set(GlenmoreParameters::$NORTH, $tileUp);
        }
        $tileDown = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x + 1, 'coord_Y' => $y
            , 'personalBoard' => $player->getPersonalBoard()]);
        if ($tileDown != null) {
            $result->set(GlenmoreParameters::$SOUTH, $tileDown);
        }
        $tileUpLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        if ($tileUpLeft != null) {
            $result->set(GlenmoreParameters::$NORTH_WEST, $tileUpLeft);
        }
        $tileUpRight = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y + 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        if ($tileUpRight != null) {
            $result->set(GlenmoreParameters::$NORTH_EAST, $tileUpRight);
        }
        $tileDownLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x + 1, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        if ($tileDownLeft != null) {
            $result->set(GlenmoreParameters::$SOUTH_WEST, $tileDownLeft);
        }
        $tileDownRight = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x + 1, 'coord_Y' => $y + 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        if ($tileDownRight != null) {
            $result->set(GlenmoreParameters::$SOUTH_EAST, $tileDownRight);
        }
        $i = 0;
        foreach($result as $tile) {
            $tile->addAdjacentTile($playerTileGLM, $i);
            $playerTileGLM->addAdjacentTile($tile, $i);
            ++$i;
            $this->entityManager->persist($playerTileGLM);
            $this->entityManager->persist($tile);
        }
        $this->entityManager->flush();
    }

    /**
     * selectResourceForFair : select a resource for fair tile
     * @param PlayerGLM          $playerGLM
     * @param PlayerTileGLM|null $playerTileGLM
     * @param ResourceGLM        $resource
     * @param Collection         $cost
     * @return void
     * @throws Exception
     */
    private function selectResourceForFair(PlayerGLM $playerGLM,
        ?PlayerTileGLM $playerTileGLM, ResourceGLM $resource, Collection $cost) : void
    {
        $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
        $numberNeeded = $cost->count();
        if ($selectedResources->count() == $numberNeeded) {
            throw new Exception("can't pick more resource for this tile");
        }
        if (!$this->canPickThisResource($selectedResources, $resource)) {
            throw new Exception("resources must be different");
        }
        $newResource = new SelectedResourceGLM();
        $newResource->setResource($resource);
        $newResource->setQuantity(1);
        $newResource->setPlayerTile($playerTileGLM);
        $newResource->setPersonalBoardGLM($playerGLM->getPersonalBoard());
        $this->entityManager->persist($newResource);
        $this->entityManager->flush();
    }

    /**
     * selectResourceForGrocer : select a resource for grocer tile
     * @param PlayerGLM          $playerGLM
     * @param PlayerTileGLM|null $playerTileGLM
     * @param ResourceGLM        $resource
     * @param Collection         $cost
     * @return void
     * @throws Exception
     */
    private function selectResourceForGrocer(PlayerGLM $playerGLM,
        ?PlayerTileGLM $playerTileGLM, ResourceGLM $resource, Collection $cost) : void
    {
        $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
        if ($selectedResources->count() > 3) {
            throw new Exception("can't pick more resource for this tile");
        }
        if (!$this->canPickThisResource($selectedResources, $resource)) {
            throw new Exception("resources must be different");
        }
        $newResource = new SelectedResourceGLM();
        $newResource->setResource($resource);
        $newResource->setQuantity(1);
        $newResource->setPlayerTile($playerTileGLM);
        $newResource->setPersonalBoardGLM($playerGLM->getPersonalBoard());
        $this->entityManager->persist($newResource);
        $this->entityManager->flush();
    }

    /**
     * selectResourceForLochOich : select a resource for grocer tile
     * @param PlayerGLM          $playerGLM
     * @param PlayerTileGLM|null $playerTileGLM
     * @param ResourceGLM        $resource
     * @param Collection         $cost
     * @return void
     * @throws Exception
     */
    private function selectResourceForLochOich(PlayerGLM $playerGLM,
        ?PlayerTileGLM $playerTileGLM, ResourceGLM $resource, Collection $cost) : void
    {
        $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
        if ($selectedResources->count() >= 2) {
            throw new Exception("can't pick more resource for this tile");
        }
        if (!$this->canPickThisResource($selectedResources, $resource)) {
            throw new Exception("resources must be different");
        }
        $newResource = new SelectedResourceGLM();
        $newResource->setResource($resource);
        $newResource->setQuantity(1);
        $newResource->setPlayerTile($playerTileGLM);
        $newResource->setPersonalBoardGLM($playerGLM->getPersonalBoard());
        $this->entityManager->persist($newResource);
        $this->entityManager->flush();
    }


    /**
     * activateFair : gives points to the player
     * @param PlayerTileGLM $tileGLM
     * @return void
     */
    private function activateFair(PlayerTileGLM $tileGLM) : void
    {
        $personalBoard = $tileGLM->getPersonalBoard();
        $selectedResources = $personalBoard->getSelectedResources();
        $points = 0;
        switch ($selectedResources->count()) {
            case 1:
                $points = 1;
                break;
            case 2:
                $points = 3;
                break;
            case 3:
                $points = 5;
                break;
            case 4:
                $points = 8;
                break;
            case 5:
                $points = 12;
                break;
        }
        foreach ($selectedResources as $selectedResource) {
            $playerTile = $selectedResource->getPlayerTile();
            foreach ($playerTile->getPlayerTileResource() as $item) {
                if ($item->getResource() === $selectedResource->getResource()) {
                    $item->setQuantity($item->getQuantity() - 1);
                    $this->entityManager->persist($item);
                    break;
                }
            }
        }
        $player = $personalBoard->getPlayerGLM();
        $player->setScore($player->getScore() + $points);
        $this->entityManager->persist($player);
        $tileGLM->setActivated(true);
        $this->entityManager->persist($tileGLM);
        $this->clearResourceSelection($player);
        $this->entityManager->flush();
    }

    /**
     * activateGrocer : gives points to the player
     * @param PlayerTileGLM $tileGLM
     * @return void
     */
    private function activateGrocer(PlayerTileGLM $tileGLM) : void
    {
        $personalBoard = $tileGLM->getPersonalBoard();
        $selectedResources = $personalBoard->getSelectedResources();
        $points = 8;
        foreach ($selectedResources as $selectedResource) {
            $playerTile = $selectedResource->getPlayerTile();
            foreach ($playerTile->getPlayerTileResource() as $item) {
                if ($item->getResource() === $selectedResource->getResource()) {
                    $item->setQuantity($item->getQuantity() - 1);
                    $this->entityManager->persist($item);
                    break;
                }
            }
        }
        $player = $personalBoard->getPlayerGLM();
        $player->setScore($player->getScore() + $points);
        $this->entityManager->persist($player);
        $tileGLM->setActivated(true);
        $this->entityManager->persist($tileGLM);
        $this->clearResourceSelection($player);
        $this->entityManager->flush();
    }

    /**
     * buyLochOich : player buys Loch Oich
     * @param PlayerGLM $playerGLM
     * @return void
     */
    private function buyLochOich(PlayerGLM $playerGLM) : void
    {
        $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
        foreach ($selectedResources as $selectedResource) {
            $playerTile = $selectedResource->getPlayerTile();
            if ($playerTile == null) {
                continue;
            }
            foreach ($playerTile->getPlayerTileResource() as $item) {
                if ($item->getResource() === $selectedResource->getResource()) {
                    $this->entityManager->remove($item);
                }
            }
        }
        $this->clearResourceSelection($playerGLM);
        $this->entityManager->flush();
    }

}