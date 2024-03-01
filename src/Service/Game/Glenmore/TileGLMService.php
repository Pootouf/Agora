<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception;

class TileGLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private PlayerGLMRepository $playerGLMRepository){}

    public function isChainBroken(MainBoardGLM $mainBoardGLM)
    {

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
        while ($this->getBoardTilesAtPosition($mainBoard, $posTile) == null)
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


}