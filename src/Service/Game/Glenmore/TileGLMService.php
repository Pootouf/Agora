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
     * @throws Exception
     */
    public function assignTileToPlayer(BoardTileGLM $tile, PlayerGLM $player) : int
    {
        // Check if condition for assign tile

        if (!$this->verifyAssignTile($tile, $player))
        {
            throw new Exception("Unable to recover tile");
        }

        $personalBoard = $player->getPersonalBoard();
        $mainBoard = $tile->getMainBoardGLM();
        $currentPosition = $player->getPawn()->getPosition();

        // Here assign tile --> Creation of player tile

        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile->getTile());
        $playerTile->setPersonalBoard($personalBoard);
        $personalBoard->addPlayerTile($playerTile);

        // Update

        $this->entityManager->persist($playerTile);
        $this->entityManager->persist($personalBoard);

        // Manage main board
        $mainBoard->removeBoardTile($tile);

        // Update

        $this->entityManager->persist($mainBoard);
        $this->entityManager->flush();

        return $currentPosition;
    }

    public function placeNewTile(PlayerGLM $player, DrawTilesGLM $drawTiles) : void
    {
        $mainBoard = $player->getGameGLM()->getMainBoard();
        $currentPosition = $player->getPawn()->getPosition();

        $posTile = $currentPosition;

        while ($this->getBoardTilesAtPosition($mainBoard, $posTile) == null)
        {
            $posTile -= 1;
            $posTile %= GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        }

        $posTile += 1;
        $posTile %= GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;

        $tile = $drawTiles->getTiles()->last();
        $drawTiles->removeTile($tile);
        $this->entityManager->persist($drawTiles);

        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setPosition($posTile);
        $boardTile->setMainBoardGLM($mainBoard);
        $this->entityManager->persist($boardTile);
        $this->entityManager->persist($mainBoard);
    }

    private function verifyAssignTile(BoardTileGLM $tile, PlayerGLM $player) : bool
    {
        return $this->canBuyTile($tile, $player)
            && $this->canPlaceTileOnPersonalBoard($tile, $player);
    }

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