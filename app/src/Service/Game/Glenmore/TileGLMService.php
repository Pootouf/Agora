<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PlayerCardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
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
                                private readonly GLMService $GLMService,
                                private readonly ResourceGLMRepository $resourceGLMRepository,
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
        $player = $this->GLMService->getActivePlayer($mainBoardGLM ->getGameGLM());
        $playerPosition = $player->getPawn()->getPosition();
        $pointerPosition = $playerPosition - 2;
        if($pointerPosition < 0){
            $pointerPosition = GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD + $pointerPosition;
        }
        $count = 0;
        while ($this->getTileInPosition($mainBoardGLM, $pointerPosition) == null){
            $pointerPosition -= 1;
            $count += 1;
            if($pointerPosition < 0){
                $pointerPosition = GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD + $pointerPosition;
            }
        }
        return $count;
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
        if (!$playerTileGLM->isActivated()) {
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
            if (!$adjacentTile->isActivated() && $adjacentTile->getTile()->getActivationBonus() != null) {
                $activableTiles->add($adjacentTile);
            }
        }

        // if every tile has been activated
        if ($activableTiles->isEmpty()) {
            $activableTiles = $this->cardGLMService->applyLochNess($personalBoard);
        }

        return $activableTiles;
    }

    /**
     * canPlaceTile: return true if the player can place the selected tile at the chosen emplacement
     *
     * @param int $x the coord x of the wanted emplacement
     * @param int $y the coord y of the wanted emplacement
     * @param TileGLM $tile
     * @param PlayerGLM $player
     * @return bool
     */
    public function canPlaceTile(int $x, int $y, TileGLM $tile, PlayerGLM $player): bool
    {
        //Recovery of the adjacent tiles
        $tileLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileRight = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x + 1, 'coord_Y' => $y
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileUp = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileDown = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x, 'coord_Y' => $y + 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileUpLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileUpRight = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x + 1, 'coord_Y' => $y - 1
            , 'personalBoard' => $player->getPersonalBoard()]);
        $tileDownLeft = $this->playerTileGLMRepository->findOneBy(['coord_X' => $x - 1, 'coord_Y' => $y + 1
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

        // Search last position busy by a tile
        while ($this->getBoardTilesAtPosition($mainBoard, $posTile) == null)
        {
            $posTile -= 1;
            if ($posTile < 0) {
                $posTile += GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
            }
        }

        // Search next position relative to the position found
        $posTile += 1;
        $posTile %= GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;

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
     * giveBuyBonus : once bought and placed, the playerTile gives bonus to its player
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    public function giveBuyBonus(PlayerTileGLM $playerTileGLM) : void
    {
        $tile = $playerTileGLM->getTile();
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $buyBonus = $tile->getBuyBonus();
        foreach ($buyBonus as $bonus) {
            $playerTileResource = new PlayerTileResourceGLM();
            $playerTileResource->setPlayerTileGLM($playerTileGLM);
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
            $cardBonus = $card->getBonus();
            if ($cardBonus != null) {
                $resource = $cardBonus->getResource();
                $playerTileResource = new PlayerTileResourceGLM();
                $playerTileResource->setQuantity($cardBonus->getAmount());
                $playerTileResource->setResource($resource);
                $this->entityManager->persist($playerTileResource);
                $playerTileGLM->addPlayerTileResource($playerTileResource);
                $this->entityManager->persist($playerTileGLM);
            }
            $this->cardGLMService->buyCardManagement($playerTileGLM);
        }
        $this->giveAllMovementPoints($playerTileGLM);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    public function activateBonus(PlayerTileGLM $tileGLM, PlayerGLM $playerGLM): void
    {
        $tile = $tileGLM->getTile();
        if($tile->getType() != GlenmoreParameters::$TILE_NAME_FAIR){
            if($this->hasPlayerEnoughResourcesToActivate($tileGLM, $playerGLM) &&
                $this->hasEnoughPlaceToActivate($tileGLM)){
                $this->givePlayerActivationBonus($tileGLM, $playerGLM);
                $this->entityManager->persist($tileGLM);
            }
        } else {
            $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
            $resourcesTypes = new ArrayCollection();
            foreach ($selectedResources as $selectedResource){
                if(!$resourcesTypes->contains($selectedResource->getResource()->getType())){
                    $resourcesTypes->add($selectedResource->getResource()->getType());
                }
            }
            $resourcesTypesCount = $resourcesTypes->count();
            $activationCostsLevels = $tileGLM->getTile()->getActivationPrice()->count();
            $selectedLevel = min($resourcesTypesCount, $activationCostsLevels);
            $activationBonus = $tileGLM->getTile()->getActivationBonus()->get($selectedLevel - 1);
            $playerGLM->setPoints($playerGLM->getPoints() + $activationBonus->getAmount());
            foreach ($selectedResources as $selectedResource){
                if($resourcesTypes->contains($selectedResource->getResource()->getType())){
                    $playerGLM->getPersonalBoard()->removeSelectedResource($selectedResource);
                    $this->entityManager->persist($playerGLM->getPersonalBoard());
                    $resourcesTypes->remove($selectedResource->getResource()->getType());
                }
            }
        }
        $tileGLM->setActivated(true);
        $this->entityManager->flush();
    }


    /**
     * Assign tile for player when conditions are verified
     * @param BoardTileGLM $boardTile
     * @param PlayerGLM $player
     * @throws Exception
     */
    public function assignTileToPlayer(BoardTileGLM $boardTile, PlayerGLM $player) : void
    {
        // Check if condition for assign tile - TODO Write condition for buy tile
        /* if (!$this->checkCanBuyTile($player, $tile))
        {
            throw new Exception("Unable to buy tile");
        } */

        // Initialization
        $personalBoard = $player->getPersonalBoard();

        // Manage personal board and update
        $personalBoard->setSelectedTile($boardTile);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * Place a selected tile in personal board's player at position (abscissa, ordinate)
     * @param PlayerGLM $player
     * @param int $abscissa
     * @param int $ordinate
     * @return void
     * @throws Exception
     */
    public function setPlaceTileAlreadySelected(PlayerGLM $player, int $abscissa, int $ordinate) : void
    {
        if ($player->getPersonalBoard()->getSelectedTile() == null)
        {
            throw new Exception("Unable to place tile");
        }

        // Initialization
        $personalBoard = $player->getPersonalBoard();
        $mainBoard = $player->getGameGLM()->getMainBoard();
        $tileSelected = $personalBoard->getSelectedTile();
        $lastPosition = $player->getPawn()->getPosition();
        $newPosition = $tileSelected->getPosition();

        // Check if condition for assign tile
        if (!$this->canPlaceTile($abscissa, $ordinate, $tileSelected->getTile(), $player))
        {
            $personalBoard->setSelectedTile(null);
            throw new Exception("Unable to place tile");
        }

        // TODO take all resource's player for buy tile

        // Here assign tile --> Creation of player tile and manage personal board
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tileSelected->getTile());
        $playerTile->setPersonalBoard($personalBoard);
        $playerTile->setCoordX($abscissa);
        $playerTile->setCoordY($ordinate);
        $this->entityManager->persist($playerTile);
        $personalBoard->addPlayerTile($playerTile);
        $personalBoard->setSelectedTile(null);

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
            foreach ($selectedResources as $selectedResource){
                if($selectedResource->getResource()->getType() == $resourceType){
                    $playerResourceCount += $selectedResource->getQuantity();
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
        $count = 0;
        foreach ($bonusResources as $bonusResource){
            $count += $bonusResource->getAmount();
        }
        return ($playerTileGLM->getPlayerTileResource()->count() + $count) <
            GlenmoreParameters::$MAX_RESOURCES_PER_TILE;
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
        $player = $this->GLMService->getActivePlayer($mainBoardGLM->getGameGLM());
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

    /*  TODO check can buy tile
        /**
         * checks if player can take tile :
         *      - If player have all resources
         * @param BoardTileGLM $tile
         * @param PlayerGLM $player
         * @return bool
         *
     private function checkCanBuyTile(PlayerGLM $player, BoardTileGLM $tile) : bool
    {
        return $this->canBuyTile($tile, $player)
            && $this->canPlaceTileOnPersonalBoard($tile, $player);
    }*/

    /**
     * Return a board tile that position is equal to parameter else null
     * @param MainBoardGLM $mainBoard
     * @param int $position
     * @return BoardTileGLM|null
     */
    private function getBoardTilesAtPosition(MainBoardGLM $mainBoard, int $position): ?BoardTileGLM
    {
        $boardTiles = $mainBoard->getBoardTiles();
        for ($i = 0; $i < count($boardTiles); $i++)
        {
           $boardTile = $boardTiles->get($i);
           if ($boardTile->getPosition() == $position)
           {
               return $boardTile;
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
     * removeTilesOfBrokenChain : removes tiles of $mainBoardGLM
     *      when chain is broken
     * @param MainBoardGLM $mainBoardGLM
     * @return void
     */
    private function removeTilesOfBrokenChain(MainBoardGLM $mainBoardGLM): void
    {
        $player = $this->GLMService->getActivePlayer($mainBoardGLM->getGameGLM());
        $playerPosition = $player->getPawn()->getPosition();
        $pointerPosition = $playerPosition - 1;
        if($pointerPosition == -1){
            $pointerPosition = GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1;
        }
        $mainBoardTiles = $mainBoardGLM->getBoardTiles();
        while (($tile = $this->getTileInPosition($mainBoardGLM, $pointerPosition)) != null){
            $mainBoardGLM->removeBoardTile($tile);
            $pointerPosition -= 1;
            if($pointerPosition == -1){
                $pointerPosition = GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1;
            }
            $this->entityManager->persist($mainBoardGLM);
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
            $playerTileResource->setResource($activationBonusResource->getResource());
            $playerTileGLM->addPlayerTileResource($playerTileResource);
            $this->entityManager->persist($playerTileGLM);
        }
        $selectedResources = $playerGLM->getPersonalBoard()->getSelectedResources();
        $activationPrices = $playerTileGLM->getTile()->getActivationPrice();
        foreach ($activationPrices as $activationPrice){
            $activationCost = $activationPrice->getPrice();
            foreach ($selectedResources as $selectedResource) {
                if($selectedResource->getResource()->getType() == $activationPrice->getResource()->getType()
                    && $activationCost > 0){
                    $playerGLM->getPersonalBoard()->removeSelectedResource($selectedResource);
                    $activationCost -= 1;
                }
            }
            if($activationCost > 0){
                throw new \Exception("Not enough resources to activate");
            }
        }
        $this->entityManager->persist($activationPrices);
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
                $playerTileResource = new PlayerTileResourceGLM();
                $playerTileResource->setPlayerTileGLM($adjacentTile);
                $playerTileResource->setResource($resource);
                $playerTileResource->setQuantity(1);
                $this->entityManager->persist($playerTileResource);
                $adjacentTile->addPlayerTileResource($playerTileResource);
                $adjacentTile->setActivated(true);
            }
        }
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }
}