<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class TileGLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private GLMService $GLMService,
        private PlayerGLMRepository $playerGLMRepository){}

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
        $player = $this->GLMService->getActivePlayer($mainBoardGLM->getGameGLM());
        $playerPosition = $player->getPawn()->getPosition();
        $pointerPosition = ($playerPosition - 2) %
            GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        $count = 0;
        while ($this->getTileInPosition($mainBoardGLM, $pointerPosition) == null){
            $count += 1;
            $pointerPosition = ($pointerPosition - 1) %
                GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        }
        return $count;
    }

    public function activateBonus(PlayerTileGLM $tileGLM, PlayerGLM $playerGLM): void
    {
        $tile = $tileGLM->getTile();
        if($tile->getType() != GlenmoreParameters::$TILE_NAME_FAIR){
            if($this->hasPlayerEnoughResourcesToActivate($tileGLM, $playerGLM) &&
                $this->hasEnoughPlaceToActivate($tileGLM)){
                $this->givePlayerActivationBonus($tileGLM, $playerGLM);
            }
        } else {
            $playerMaximumItemsToExchange = $this->getMaximumTypeItemsToExchange($playerGLM);
            $tileMaximumItemsToExchange = $tileGLM->getTile()->getActivationBonus()->count();
            $itemToExchange = $playerMaximumItemsToExchange;
            if($tileMaximumItemsToExchange < $playerMaximumItemsToExchange){
                $itemToExchange = $tileMaximumItemsToExchange;
            }

        }
    }

    /**
     * Assign tile for player when conditions are verified
     * @param BoardTileGLM $boardTile
     * @param PlayerGLM $player
     * @return int
     * @throws Exception
     */
    public function assignTileToPlayer(BoardTileGLM $boardTile, PlayerGLM $player) : int
    {
        // Check if condition for assign tile - TODO Write condition for assign tile
        /* if (!$this->verifyAssignTile($tile, $player))
        {
            throw new Exception("Unable to recover tile");
        } */

        // Initialization
        $personalBoard = $player->getPersonalBoard();
        $mainBoard = $boardTile->getMainBoardGLM();
        $lastPosition = $player->getPawn()->getPosition();
        $newPosition = $boardTile->getPosition();

        // Here assign tile --> Creation of player tile
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($boardTile->getTile());
        $playerTile->setPersonalBoard($personalBoard);
        $personalBoard->addPlayerTile($playerTile);

        // Set new position of pawn's player and update
        $player->getPawn()->setPosition($newPosition);
        $this->entityManager->persist($player);

        // Update
        $this->entityManager->persist($playerTile);
        $this->entityManager->persist($personalBoard);

        // Manage main board and update
        $mainBoard->removeBoardTile($boardTile);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->flush();

        // Return last position of player
        return $lastPosition;
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
        while ($this->getTileInPosition($mainBoard, $posTile) == null)
        {
            $posTile -= 1;
            $posTile %= GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        }

        // Search next position relative to the position found
        $posTile += 1;
        $posTile %= GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;

        // Manage draw tile and update
        $tile = $drawTiles->getTiles()->last();
        $drawTiles->removeTile($tile);
        $this->entityManager->persist($drawTiles);

        // Manage main board and update
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setPosition($posTile);
        $boardTile->setMainBoardGLM($mainBoard);
        $this->entityManager->persist($boardTile);
        $this->entityManager->persist($mainBoard);

        $this->entityManager->flush();
    }

    /*
        /**
         * checks if player can take tile :
         *      - If player have all resources
         *      - If player can place tile on personalBoard
         * @param BoardTileGLM $tile
         * @param PlayerGLM $player
         * @return bool
         *
     private function verifyAssignTile(BoardTileGLM $tile, PlayerGLM $player) : bool
    {
        return $this->canBuyTile($tile, $player)
            && $this->canPlaceTileOnPersonalBoard($tile, $player);
    }*/

    private function getMaximumTypeItemsToExchange(PlayerGLM $playerGLM): int
    {
        $resourcesTypes = new ArrayCollection();
        $playerTiles = $playerGLM->getPersonalBoard()->getPlayerTiles();
        foreach ($playerTiles as $playerTile){
            $resources = $playerTile->getResources();
            foreach ($resources as $resource){
                if(!$resourcesTypes->contains($resource->getType())){
                    $resourcesTypes->add($resource->getType());
                }
            }
        }
        return $resourcesTypes->count();
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
        $positionBehindPlayer = ($playerPosition - 1) %
            GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        $mainBoardTiles = $mainBoardGLM->getBoardTiles();
        foreach ($mainBoardTiles as $boardTile){
            if($boardTile->getPosition() == $positionBehindPlayer){
                return true;
            }
        }
        return false;
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
     * hasPlayerEnoughResourcesToActivate : checks if player has enough resources to activate the tile
     * @param PlayerTileGLM $playerTileGLM
     * @param PlayerGLM $playerGLM
     * @return bool
     */
    private function hasPlayerEnoughResourcesToActivate(PlayerTileGLM $playerTileGLM, PlayerGLM $playerGLM): bool
    {
        $tileGLM = $playerTileGLM->getTile();
        $activationPrices = $tileGLM->getActivationPrice();
        $playerTiles = $playerGLM->getPersonalBoard()->getPlayerTiles();
        foreach ($activationPrices as $activationPrice){
            $resourceType = $activationPrice->getResource();
            $resourceAmount = $activationPrice->getPrice();
            $playerResourceCount = 0;
            foreach ($playerTiles as $playerTile){
                $resourcesOnTile = $playerTile->getResources();
                foreach ($resourcesOnTile as $resource){
                    if($resource->getType() == $resourceType){
                        $playerResourceCount += 1;
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
     * hasEnoughPlaceToActivate : checks if player has enough space on the tile to get resources
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
        return ($playerTileGLM->getResources()->count() + $count) < GlenmoreParameters::$MAX_RESOURCES_PER_TILE;
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
        $pointerPosition = ($playerPosition - 1) %
            GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        $mainBoardTiles = $mainBoardGLM->getBoardTiles();
        while ($this->getTileInPosition($mainBoardGLM, $pointerPosition) != null){
           foreach ($mainBoardTiles as $boardTile){
               if($boardTile->getPosition() == $pointerPosition){
                   $mainBoardGLM->removeBoardTile($boardTile);
               }
           }
           $pointerPosition = ($pointerPosition - 1) %
               GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
           $this->entityManager->persist($mainBoardGLM);
        }
        $this->entityManager->flush();
    }

    /**
     * givePlayerActivationBonus : gives the player his resources
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function givePlayerActivationBonus(PlayerTileGLM $playerTileGLM): void
    {
        $tileGLM = $playerTileGLM->getTile();
        $activationBonusResources = $tileGLM->getActivationBonus();
        foreach ($activationBonusResources as $activationBonusResource){
            $playerTileGLM->addResource($activationBonusResource->getResource());
            $this->entityManager->persist($playerTileGLM);
        }
        $this->entityManager->flush();
    }
}