<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

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
    public function sellResource(PlayerGLM $player
        , ResourceGLM $resource) : void
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
        $tileResource = $this->getResourceOnPersonalBoard(
            $personalBoard,
            $resource
        );

        if ($tileResource != null || $tileResource->getQuantity() == 0)
        {
            throw new Exception("Unable to sell the resource");
        }

        // Manage resource and update
        $tileResource->setQuantity($tileResource->getQuantity() - 1);
        $personalBoard->setMoney($personalBoard->getMoney() + $money);
        $this->removeResourceInWarehouse($warehouse, $resource);
        $this->entityManager->persist($tileResource);
        $this->entityManager->persist($personalBoard);

        $this->entityManager->flush();
    }

    /**
     * Return the price of a resource
     * @param WarehouseGLM $warehouse
     * @param ResourceGLM $resource
     * @return int
     */
    private function getMoneyAvailable(WarehouseGLM $warehouse
        , ResourceGLM $resource) : int
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
    private function getResourceOnPersonalBoard(PersonalBoardGLM $personalBoard
        , ResourceGLM $resource) : ?PlayerTileResourceGLM
    {

        // Search this resource in personal board
        $playerTiles = $personalBoard->getPlayerTiles();
        foreach ($playerTiles as $t)
        {
            $tilesResource = $t->getPlayerTileResource();
            foreach ($tilesResource as $tile)
            {
                $r = $tile->getResource();
                if ($r->getColor() === $resource->getColor())
                {
                    return $tile;
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