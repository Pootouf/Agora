<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\SelectedResourceGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class WarehouseGLMService
{

    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly TileGLMService         $tileGLMService){}


    /**
     * sellResource : A player sells one resource when conditions are verified
     * @param PlayerGLM $player
     * @param ResourceGLM $resource
     * @param SelectedResourceGLM $selectedResourceGLM
     * @return void
     * @throws Exception
     */
    public function sellResource(PlayerGLM $player
        , ResourceGLM $resource, SelectedResourceGLM $selectedResourceGLM) : void
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
            throw new Exception("Unable to sell the resource, there is no such resource in warehouse");
        }

        $money = GlenmoreParameters::MONEY_FROM_QUANTITY[$warehouseLine->getQuantity() - 1];
        if ($money == GlenmoreParameters::MIN_TRADE)
        {
            throw new Exception("Unable to sell the resource, there is no place to sell");
        }


        if ($selectedResourceGLM->getQuantity() == 0)
        {
            throw new Exception("Unable to sell the resource, player tile does not have enough resource");
        }

        // Manage resource and update
        $playerTileResource = $selectedResourceGLM->getPlayerTile()->getPlayerTileResource()
            ->filter(function (PlayerTileResourceGLM $playerTileResource) use ($resource) {
                return $playerTileResource->getResource()->getId() == $resource->getId()
                    && $playerTileResource->getQuantity() > 0;
            })->first();
        if(!$playerTileResource) {
            throw new Exception("Unable to sell the resource, impossible to find the tile resource");
        }
        $playerTileResource->setQuantity($playerTileResource->getQuantity() - 1);
        $personalBoard->setMoney($personalBoard->getMoney() + $money);
        $this->manageWarehouseLine($warehouseLine, $money, false);
        $this->entityManager->persist($playerTileResource);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->remove($selectedResourceGLM);
        $this->entityManager->flush();
    }

    /**
     * buyResourceFromWarehouse : A player buys one resource when conditions are satisfied
     * @param PlayerGLM $player
     * @param ResourceGLM $resource
     * @return void
     * @throws Exception
     */
    public function buyResourceFromWarehouse(PlayerGLM $player, ResourceGLM $resource): void
    {
        $warehouse = $player->getGameGLM()->getMainBoard()->getWarehouse();
        $warehouseLine = $this->getWarehouseLineOfResource(
            $warehouse,
            $resource
        );
        if ($warehouseLine == null) {
            throw new Exception("Unable to buy the resource, not a valid type");
        }
        if ($warehouseLine->getQuantity() >= GlenmoreParameters::MAX_TRADE) {
            throw new Exception('No resource to buy');
        }

        $moneyOfPlayer = $player->getPersonalBoard()->getMoney();
        $moneyNeeded = GlenmoreParameters::MONEY_FROM_QUANTITY[$warehouseLine->getQuantity()];
        if ($moneyOfPlayer < $moneyNeeded) {
            throw new Exception('Not enough money to buy resource');
        }
        $buyingTile = $player->getPersonalBoard()->getBuyingTile();
        $activatingTile = $player->getPersonalBoard()->getActivatedTile();
        if ($buyingTile == null && $activatingTile == null) {
            throw new Exception("player does not need to buy resources, not correct phase");
        }

        $playerResources = $this->tileGLMService->getPlayerProductionResources($player);
        $found = false;
        if ($buyingTile != null) {
            if ($buyingTile->getBoardTile()->getTile()->getName() === GlenmoreParameters::CARD_LOCH_OICH) {
                if ($player->getPersonalBoard()->getSelectedResources()->contains($resource)) {
                    throw new Exception("player does not need to buy this resource for Loch Oich");
                }
            } else {
                $buyPrice = $buyingTile->getBoardTile()->getTile()->getBuyPrice();
                foreach ($buyPrice as $price) {
                    if ($price->getResource() === $resource) {
                        $requiredResource = $price;
                        $found = true;
                        if ($playerResources[$resource->getColor()] >= $requiredResource->getPrice()) {
                            throw new Exception("player does not need to buy this resource, too much possessed to buy");
                        }
                    }
                }
            }
            if (!$found) {
                throw new Exception("player does not need to buy this resource to buy");
            }
        } else if ($activatingTile != null) {
            $activationPrice = $activatingTile->getTile()->getActivationPrice();
            foreach ($activationPrice as $price) {
                if ($price->getResource() === $resource) {
                    $requiredResource = $price;
                    $found = true;
                    if ($playerResources[$resource->getColor()] >= $requiredResource->getPrice()) {
                        throw new Exception("player does not need to buy this resource too much possessed to activate");
                    }
                }
            }
            if (!$found) {
                throw new Exception("player does not need to buy this resource to activate");
            }
        }
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setPlayerTile(null);
        $selectedResource->setResource($resource);
        $selectedResource->setPersonalBoardGLM($player->getPersonalBoard());
        $selectedResource->setQuantity(1);
        $this->entityManager->persist($selectedResource);

        $player->getPersonalBoard()->setMoney($moneyOfPlayer - $moneyNeeded);
        $this->entityManager->persist($player->getPersonalBoard());

        $this->manageWarehouseLine($warehouseLine, $moneyNeeded, true);
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


    private function manageWarehouseLine(WarehouseLineGLM $warehouseLine, int $money, bool $isPurchasable) : void
    {
        if ($isPurchasable) {
            $warehouseLine->setQuantity($warehouseLine->getQuantity() + 1);
            $warehouseLine->setCoinNumber($warehouseLine->getCoinNumber() + $money);
        } else {
            $warehouseLine->setQuantity($warehouseLine->getQuantity() - 1);
            $warehouseLine->setCoinNumber($warehouseLine->getCoinNumber() - $money);
        }
        $this->entityManager->persist($warehouseLine);
    }
}