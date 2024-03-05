<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PlayerCardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class TileGLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private GLMService $GLMService,
        private PlayerGLMRepository $playerGLMRepository,
        private CardGLMService $cardGLMService){}

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
            GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
        $count = 0;
        while ($this->getTileInPosition($mainBoardGLM, $pointerPosition) == null){
            $count += 1;
            $pointerPosition = ($pointerPosition - 1) %
                GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
        }
        return $count;
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
     * getActivableTiles : returns a collection of all activable tiles after a new tile was placed
     *  onto personalBoard
     * @param PlayerTileGLM $playerTileGLM
     * @return ArrayCollection<Int, PlayerTileGLM>
     */
    public function getActivableTiles(PlayerTileGLM $playerTileGLM) : ArrayCollection
    {
        $activableTiles = new ArrayCollection();
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
        if ($adjacentTiles->isEmpty()) {
            $activableTiles = $this->cardGLMService->applyLochNess($personalBoard);
        }

        return $activableTiles;
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
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
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
            GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
        $mainBoardTiles = $mainBoardGLM->getBoardTiles();
        foreach ($mainBoardTiles as $boardTile){
            if($boardTile->getPosition() == $positionBehindPlayer){
                return true;
            }
        }
        return false;
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
        $pointerPosition = ($playerPosition - 1) %
            GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
        $mainBoardTiles = $mainBoardGLM->getBoardTiles();
        while ($this->getTileInPosition($mainBoardGLM, $pointerPosition) != null){
           foreach ($mainBoardTiles as $boardTile){
               if($boardTile->getPosition() == $pointerPosition){
                   $mainBoardGLM->removeBoardTile($boardTile);
               }
           }
           $pointerPosition = ($pointerPosition - 1) %
               GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
           $this->entityManager->persist($mainBoardGLM);
        }
        $this->entityManager->flush();
    }
}