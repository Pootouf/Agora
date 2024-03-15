<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
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
use App\Entity\Game\Glenmore\TileBuyCostGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileResourceGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class TileGLMService
{

    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly ResourceGLMRepository $resourceGLMRepository,
                                private readonly PlayerGLMRepository $playerGLMRepository,
                                private readonly PlayerTileResourceGLMRepository $playerTileResourceGLMRepository,
                                private readonly PlayerTileGLMRepository $playerTileGLMRepository,
                                private readonly CardGLMService $cardGLMService){}



    /**
     * getAmountOfTileToReplace : returns the amount of tiles to replace
     * @param MainBoardGLM $mainBoardGLM
     * @return int
     */
    public function getAmountOfTileToReplace(MainBoardGLM $mainBoardGLM): int
    {
        if(!$this->isChainBroken($mainBoardGLM)){
            return 1;
        }
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
            $personalBoard->getPlayerGLM()->setRoundPhase(GlenmoreParameters::$MOVEMENT_PHASE);
            $this->entityManager->persist($personalBoard->getPlayerGLM());
            $this->entityManager->flush();
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
        foreach ($tile->getBuyPrice() as $buyPrice) {
            $resourceTile = $buyPrice->getResource();
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
                return false;
            }
        }
        return true;
    }

    /**
     * giveBuyBonus : once bought and placed, the playerTile gives bonus to its player
     * @param PlayerTileGLM $playerTileGLM
     * @return int
     */
    public function giveBuyBonus(PlayerTileGLM $playerTileGLM) : int
    {
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
            return $this->cardGLMService->buyCardManagement($playerTileGLM);
        }
        $this->giveAllMovementPoints($playerTileGLM);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        return 0;
    }

    /**
     * getMovementPoints : returns total movement points of a player
     * @param PlayerTileGLM $playerTileGLM
     * @return int
     */
    public function getMovementPoints(PlayerTileGLM $playerTileGLM) : int {
        $tiles = new ArrayCollection();
        $tiles->add($playerTileGLM);
        foreach ($playerTileGLM->getAdjacentTiles() as $adjacentTile) {
            $tiles->add($adjacentTile);
        }
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
        $this->addSelectedResourcesFromTileWithCost($playerTileGLM, $resource, $selectedTile->getTile()->getBuyPrice());
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
        $this->addSelectedResourcesFromTileWithCost($playerTileGLM, $resource,
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
        $globalResources = $player->getPersonalBoard()->getSelectedResources();
        foreach ($tile->getBuyPrice() as $buyPrice) {
            $priceTile = $buyPrice->getPrice();
            $resource = $buyPrice->getResource();
            $selectedResourcesOfSameResource = $globalResources->filter(function (SelectedResourceGLM $selectedResourceGLM) use ($resource) {
               return $selectedResourceGLM->getResource()->getId() == $resource->getId();
            });
            if ($selectedResourcesOfSameResource->count() != $priceTile) {
                throw new \Exception('Invalid amount of selected resources');
            }
        }
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
                    $priceTile -= $resource->getQuantity();
                    $player->getPersonalBoard()->removeSelectedResource($resource);
                    $playerTileResource = $playerTile->getPlayerTileResource()->filter(
                        function (PlayerTileResourceGLM $playerTileResourceGLM) use ($resource) {
                            return $playerTileResourceGLM->getResource()->getId() == $resource->getResource()->getId();
                        })->first();
                    $this->entityManager->remove($playerTileResource);
                    $this->entityManager->remove($resource);
                }
                if ($priceTile == 0) {
                    break;
                }
            }
        }
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
        while ($this->getBoardTilesAtPosition($mainBoard, $posTile) == null && $this->getPawnsAtPosition($player, $mainBoard, $posTile) == null)
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
     * activateBonus : activate a tile and give player his resources
     * @param PlayerTileGLM $tileGLM
     * @param PlayerGLM $playerGLM
     * @return void
     * @throws \Exception
     */
    public function activateBonus(PlayerTileGLM $tileGLM, PlayerGLM $playerGLM): void
    {
        $tile = $tileGLM->getTile();
        if(!$this->hasPlayerEnoughResourcesToActivate($tileGLM, $playerGLM)){
            throw new \Exception("NOT ENOUGH RESOURCES");
        }
        if(!$this->hasEnoughPlaceToActivate($tileGLM)) {
            throw new \Exception("NOT ENOUGH PLACE ON TILE");
        }
        if($tile->getType() != GlenmoreParameters::$TILE_TYPE_BROWN){
            $this->givePlayerActivationBonus($tileGLM, $playerGLM);
            $this->entityManager->persist($tileGLM);
        } else {
            $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
            $resourcesTypes = new ArrayCollection();
            foreach ($selectedResources as $selectedResource){
                if(!$resourcesTypes->contains($selectedResource->getResource()->getColor())){
                    $resourcesTypes->add($selectedResource->getResource()->getColor());
                }
            }
            $resourcesTypesCount = $resourcesTypes->count();
            $activationCostsLevels = $tileGLM->getTile()->getActivationPrice()->count();
            $selectedLevel = min($resourcesTypesCount, $activationCostsLevels);
            $activationBonus = $tileGLM->getTile()->getActivationBonus()->get($selectedLevel - 1);
            $playerGLM->setPoints($playerGLM->getPoints() + $activationBonus->getAmount());
            foreach ($selectedResources as $selectedResource){
                if($resourcesTypes->contains($selectedResource->getResource()->getColor())){
                    $playerGLM->getPersonalBoard()->removeSelectedResource($selectedResource);
                    $this->entityManager->persist($playerGLM->getPersonalBoard());
                    $resourcesTypes->remove($selectedResource->getResource()->getColor());
                }
            }
            $this->entityManager->persist($playerGLM);
        }
        $tileGLM->setActivated(true);
        $this->entityManager->flush();
    }

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
     * @return void
     * @throws Exception
     */
    public function moveVillager(PlayerTileGLM $playerTileGLM, int $direction) : void
    {
        $targetedTile = $this->getTargetedTile($playerTileGLM, $direction);
        if($targetedTile == null) {
            throw new Exception("no targeted tile in this direction");
        }

        if (!$this->doesTileContainsVillager($playerTileGLM)) {
            throw new Exception("no villager placed on this tile");
        }
        $player = $playerTileGLM->getPersonalBoard()->getPlayerGLM();
        $movementPoint = $this->getMovementPoints($playerTileGLM);
        if ($movementPoint <= 0) {
            throw new Exception("no more movement points");
        }
        // removes villager from the tile
        $villager = $this->retrieveVillagerFromTile($playerTileGLM);
        // places villager onto wanted tile
        $this->placeVillagerOntoTile($villager, $targetedTile);
        // removes a movement point from the player
        $this->lowerMovementPoints($player);
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
        $movementPoint = $this->getMovementPoints($playerTileGLM);
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
     * Assign tile for player when conditions are verified
     * @param BoardTileGLM $boardTile
     * @param PlayerGLM $player
     * @throws Exception
     */
    public function assignTileToPlayer(BoardTileGLM $boardTile, PlayerGLM $player) : void
    {
        if (!$this->canBuyTile($boardTile->getTile(), $player)) {
            throw new Exception("Unable to buy tile");
        }
        if ($player->getPersonalBoard()->getBuyingTile() != null) {
            throw new Exception("Already bought a tile");
        }
        // Initialization
        $personalBoard = $player->getPersonalBoard();

        // Manage personal board and update
        $personalBoard->setBuyingTile($boardTile);
        $this->entityManager->persist($personalBoard);
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
            throw new Exception("Unable to place tile");
        }

        // Initialization
        $personalBoard = $player->getPersonalBoard();
        $mainBoard = $player->getGameGLM()->getMainBoard();
        $tileSelected = $personalBoard->getBuyingTile();
        $lastPosition = $player->getPawn()->getPosition();
        $newPosition = $tileSelected->getPosition();

        // Check if condition for assign tile
        if (!$this->canPlaceTile($abscissa, $ordinate, $tileSelected->getTile(), $player))
        {
            $personalBoard->setBuyingTile(null);
            throw new Exception("Unable to place tile");
        }

        $this->buyTile($tileSelected->getTile(), $player);

        // Here assign tile --> Creation of player tile and manage personal board
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tileSelected->getTile());
        $playerTile->setPersonalBoard($personalBoard);
        $playerTile->setCoordX($abscissa);
        $playerTile->setCoordY($ordinate);
        $this->entityManager->persist($playerTile);
        $this->manageAdjacentTiles($abscissa, $ordinate, $player, $playerTile);
        $personalBoard->addPlayerTile($playerTile);
        $personalBoard->setBuyingTile(null);
        // Manage main board
        $mainBoard->removeBoardTile($tileSelected);
        $mainBoard->setLastPosition($lastPosition);
        // Set new position of pawn's player
        $player->getPawn()->setPosition($newPosition);
        // Update
        $this->entityManager->persist($personalBoard);
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
     * @return bool
     */
    private function hasPlayerEnoughResourcesToActivate(PlayerTileGLM $playerTileGLM, PlayerGLM $playerGLM): bool
    {
        $tileGLM = $playerTileGLM->getTile();
        $activationPrices = $tileGLM->getActivationPrice();
        $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
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
            } else {
                foreach ($selectedResources as $selectedResource){
                    if($selectedResource->getResource()->getType() == $resourceType){
                        $playerResourceCount += $selectedResource->getQuantity();
                    }
                }
            }
            if($playerResourceCount < $resourceAmount){
                return false;
            }
        }
        return true;
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
        return $this->playerTileResourceGLMRepository->findOneBy(['resource' => $villager->getId(),
                'playerTileGLM' => $playerTile->getId()]) != null;
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
        return count($villagers);
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
     * isChainBroken : returns true if the chain is broken, false otherwise
     * @param MainBoardGLM $mainBoardGLM
     * @return bool
     */
    private function isChainBroken(MainBoardGLM $mainBoardGLM): bool
    {
        $player = $this->getActivePlayer($mainBoardGLM->getGameGLM());
        $playerPosition = $player->getPawn()->getPosition();
        $positionBehindPlayer = $playerPosition - 1;
        if($positionBehindPlayer == -1){
            $positionBehindPlayer = GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1;
        }
        if($this->getTileInPosition($mainBoardGLM, $positionBehindPlayer) != null){
            return true;
        }
        return false;
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
    private function getBoardTilesAtPosition(MainBoardGLM $mainBoard, int $position): BoardTileGLM|null
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
           if ($pawn->getPlayerGLM() !== $playerGLM) {
               $pawns->add($pawn);
           }
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
     * @param PlayerTileGLM $playerTileGLM
     * @param ResourceGLM $resource
     * @param Collection<TileBuyCostGLM> $cost cost of the tile
     * @return void
     * @throws \Exception if the selected resources can't be used to buy the tile
     */
    public function addSelectedResourcesFromTileWithCost(PlayerTileGLM $playerTileGLM, ResourceGLM $resource, Collection $cost) : void
    {
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $selectedResources = $personalBoard->getSelectedResources();
        $selectedResourcesLikeResource = $selectedResources->filter(
            function (SelectedResourceGLM $selectedResourceGLM) use ($resource) {
                return $selectedResourceGLM->getResource()->getId() == $resource->getId();
            });

        $numberOfSelectedResources = 0;
        foreach ($selectedResourcesLikeResource as $selectedResourceLikeResource) {
            $numberOfSelectedResources += $selectedResourceLikeResource->getQuantity();
        }

        $priceCost = $cost->filter(
            function (TileBuyCostGLM $buyCost) use ($resource) {
                return $buyCost->getResource()->getId() == $resource->getId();
            })->first();
        $priceCost = !$priceCost ? 0 : $priceCost->getPrice();
        if ($numberOfSelectedResources >= $priceCost) {
            throw new \Exception('Impossible to choose this resource');
        }

        $selectedResourceWithSamePlayerTile = $selectedResourcesLikeResource->filter(
            function (SelectedResourceGLM $selectedResourceGLM) use ($playerTileGLM) {
                return $selectedResourceGLM->getPlayerTile()->getId() == $playerTileGLM->getId();
            }
        )->first();
        if (!$selectedResourceWithSamePlayerTile) {
            $selectedResource = new SelectedResourceGLM();
            $selectedResource->setPlayerTile($playerTileGLM);
            $selectedResource->setResource($resource);
            $selectedResource->setQuantity(1);
            $selectedResource->setPersonalBoardGLM($playerTileGLM->getPersonalBoard());
            $this->entityManager->persist($selectedResource);
        } else {
            $selectedResourceWithSamePlayerTile
                ->setQuantity($selectedResourceWithSamePlayerTile->getQuantity() + 1);
            $this->entityManager->persist($selectedResourceWithSamePlayerTile);
        }

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
            $playerTileResource = new PlayerTileResourceGLM();
            $playerTileResource->setQuantity($activationBonusResource->getAmount());
            $playerTileResource->setPlayer($playerGLM);
            $playerTileResource->setResource($activationBonusResource->getResource());
            $this->entityManager->persist($playerTileResource);
            $playerTileGLM->addPlayerTileResource($playerTileResource);
            $this->entityManager->persist($playerTileGLM);
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
                $exists = false;
                $resource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$MOVEMENT_RESOURCE]);
                foreach ($adjacentTile->getPlayerTileResource() as $tileResource) {
                    if ($tileResource->getResource()->getType() === GlenmoreParameters::$MOVEMENT_RESOURCE) {
                        $tileResource->setQuantity(1);
                        $this->entityManager->persist($tileResource);
                        $adjacentTile->addPlayerTileResource($tileResource);
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
}