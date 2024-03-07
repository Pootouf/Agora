<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
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
        $warehouseLine = $this->getWarehouseLineOfResource(
            $warehouse,
            $resource
        );
        if ($warehouseLine == null)
        {
            throw new Exception("Unable to sell the resource");
        }

        $money = GlenmoreParameters::$MONEY_FROM_QUANTITY[$warehouseLine->getQuantity()];
        if ($money == GlenmoreParameters::$MIN_TRADE)
        {
            throw new Exception("Unable to sell the resource");
        }


        // Check if the player has the resource
        $tileResource = $this->getResourceOnPersonalBoard(
            $personalBoard,
            $resource
        );

        if ($tileResource == null || $tileResource->getQuantity() == 0)
        {
            throw new Exception("Unable to sell the resource");
        }

        // Manage resource and update
        $tileResource->setQuantity($tileResource->getQuantity() - 1);
        $personalBoard->setMoney($personalBoard->getMoney() + $money);
        $this->manageWarehouseLine($warehouseLine, $money, false);
        $this->entityManager->persist($tileResource);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * Return the price of a resource
     * @param WarehouseGLM $warehouse
     * @param ResourceGLM $resource
     * @return WarehouseLineGLM|null
     */
    private function getWarehouseLineOfResource(WarehouseGLM $warehouse
        , ResourceGLM $resource) : ?WarehouseLineGLM
    {
        $money = GlenmoreParameters::$MIN_TRADE;
        $warehouseLine = $warehouse->getWarehouseLine();
        foreach ($warehouseLine as $w)
        {
            if ($w->getResource()->getColor() === $resource->getColor())
            {
                return $w;
            }
        }
        return null;
    }

    /**
     * Return a tile's player or null that contains resource
     * @param PersonalBoardGLM $personalBoard
     * @param ResourceGLM $resource
     * @return PlayerTileGLM|null
     */
    public function getResourceOnPersonalBoard(PersonalBoardGLM $personalBoard
        , ResourceGLM $resource) : ?PlayerTileResourceGLM
    {

        // Search this resource in personal board
        $playerTiles = $personalBoard->getPlayerTiles();
        foreach ($playerTiles as $t)
        {
            $tilesResource = $t->getPlayerTileResource();
            foreach ($tilesResource as $tile)
            {
                if ($tile->getQuantity() > 0)
                {
                    $r = $tile->getResource();
                    if ($r->getColor() === $resource->getColor())
                    {
                        return $tile;
                    }
                }
            }
        }
        return null;
    }

    private function manageWarehouseLine(WarehouseLineGLM $warehouseLine, int $money, bool $isPurchasable) : void
    {
        switch ($isPurchasable)
        {
            case true:
                return; // TODO Etienne
            case false:
                $warehouseLine->setQuantity($warehouseLine->getQuantity() - 1);
                $warehouseLine->setCoinNumber($warehouseLine->getCoinNumber() - $money);
        }
        $this->entityManager->persist($warehouseLine);
    }
}