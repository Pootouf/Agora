<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
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
        }
    }
}