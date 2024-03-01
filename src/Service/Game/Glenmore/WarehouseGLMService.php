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
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception;

class WarehouseGLMService
{

    public function __construct(private EntityManagerInterface $entityManager,
                                private GLMService $GLMService,
                                private PlayerGLMRepository $playerGLMRepository){}


    /**
     * A player sell one resource when conditions are verified
     * @param PlayerGLM $player
     * @param ResourceGLM $resource
     * @return void
     * @throws Exception
     */
    public function sellResource(PlayerGLM $player, ResourceGLM $resource) : void
    {
        // Initialization
        $personalBoard = $player->getPersonalBoard();
        $mainBoard = $player->getGameGLM()->getMainBoard();
        $warehouse = $mainBoard->getWarehouse();

        // Check if money available
        $money = $this->getMoneyAvailable($warehouse, $resource);
        if ($money == GlenmoreParameters::$MIN_TRADE)
        {
            throw new Exception("Unable to sell the resource");
        }

        // Check if the player has the resource
        $playerTile = $this->hasResource($personalBoard, $resource);
        if ($playerTile != null)
        {
            throw new Exception("Unable to sell the resource");
        }

        // Manage resource and update
        $playerTile->removeResource($resource);
        $personalBoard->setMoney($personalBoard->getMoney() + $money);
        $this->removeResourceInWarehouse($warehouse, $resource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->persist($personalBoard);

        $this->entityManager->flush();
    }

    /**
     * Return the price of a resource
     * @param WarehouseGLM $warehouse
     * @param ResourceGLM $resource
     * @return int
     */
    private function getMoneyAvailable(WarehouseGLM $warehouse, ResourceGLM $resource) : int
    {
        $money = 0;
        $resources = $warehouse->getResources();
        foreach ($resources as $r)
        {
            if ($r->getColor() === $resource->getColor())
            {
                $money += 1;
            }
        }
        return $money;
    }

    /**
     * Return a tile's player or null that contains resource
     * @param PersonalBoardGLM $personalBoard
     * @param ResourceGLM $resource
     * @return PlayerTileGLM|null
     */
    private function hasResource(PersonalBoardGLM $personalBoard, ResourceGLM $resource) : ?PlayerTileGLM
    {

        // Search this resource in personal board
        $playerTiles = $personalBoard->getPlayerTiles();
        foreach ($playerTiles as $t)
        {
            $resources = $t->getResources();
            foreach ($resources as $r)
            {
                if ($r === $resource)
                {
                    return $t;
                }
            }
        }
        return null;
    }

    /**
     * Remove resource from warehouse
     * @param WarehouseGLM $warehouse
     * @param ResourceGLM $resource
     * @return void
     */
    private function removeResourceInWarehouse(WarehouseGLM $warehouse, ResourceGLM $resource) : void
    {
        $resources = $warehouse->getResources();
        foreach ($resources as $r)
        {
            if ($r->getColor() === $resource->getColor())
            {
                $warehouse->removeResource($r);
                $this->entityManager->persist($warehouse);
                return;
            }
        }
    }

}