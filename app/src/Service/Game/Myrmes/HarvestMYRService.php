<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class HarvestMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService)
    {}

    /**
     * areAllPheromonesHarvested : indicate if a player has ended its harvest obligatory phase
     * @param PlayerMYR $playerMYR
     * @return bool
     */
    public function areAllPheromonesHarvested(PlayerMYR $playerMYR): bool
    {
        $pheromones = $playerMYR->getPheromonMYRs();
        foreach ($pheromones as $pheromone) {
            if(!$pheromone->isHarvested()) {
                return false;
            }
        }
        return true;
    }

    /**
     * canStillHarvest : indicate if a player is still in harvest phase
     * @param PlayerMYR $playerMYR
     * @return bool
     */
    public function canStillHarvest(PlayerMYR $playerMYR): bool
    {
        return $this->areAllPheromonesHarvested($playerMYR) && $playerMYR->getRemainingHarvestingBonus() > 0
            || !$this->areAllPheromonesHarvested($playerMYR);
    }

    /**
     * harvestPheromone : remove the resources which is on tile and gives it to the player
     * @param PlayerMYR $playerMYR
     * @param TileMYR $tileMYR
     * @return void
     * @throws Exception
     */
    public function harvestPheromone(PlayerMYR $playerMYR, TileMYR $tileMYR) : void
    {
        $playerPheromones = $playerMYR->getPheromonMYRs();
        foreach ($playerPheromones as $playerPheromone) {
            foreach ($playerPheromone->getPheromonTiles() as $tile) {
                if($tile->getTile() === $tileMYR) {
                    if($playerPheromone->isHarvested()) {
                        if($playerMYR->getRemainingHarvestingBonus() <= 0) {
                            throw new Exception("Pheromone already harvested");
                        }
                        $playerMYR->setRemainingHarvestingBonus(
                            $playerMYR->getRemainingHarvestingBonus() - 1);
                        $this->entityManager->persist($playerMYR);
                    }
                    $resource = $tile->getResource();
                    $tile->setResource(null);
                    $playerResources = $playerMYR->getPersonalBoardMYR()->getPlayerResourceMYRs();
                    foreach ($playerResources as $playerResource) {
                        if($playerResource->getResource()->getDescription() == $resource->getDescription()) {
                            $playerResource->setQuantity($playerResource->getQuantity() + 1);
                            $playerPheromone->setHarvested(true);
                            $this->entityManager->persist($playerResource);
                            $this->entityManager->persist($playerPheromone);
                        }
                    }
                }
            }
        }
        $this->entityManager->flush();
    }

    /**
     * harvestPlayerFarms : activates all the player farms
     * @param PlayerMYR $playerMYR
     * @return void
     */
    public function harvestPlayerFarms(PlayerMYR $playerMYR) : void
    {
        $playerPheromones = $playerMYR->getPheromonMYRs();
        foreach ($playerPheromones as $playerPheromone) {
            $tileType = $playerPheromone->getType();
            switch ($tileType->getType()) {
                case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
                    foreach ($playerMYR->getPersonalBoardMYR()->getPlayerResourceMYRs() as $playerResource) {
                        if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS) {
                            $playerPheromone->setHarvested(true);
                            $this->entityManager->persist($playerPheromone);
                            $playerResource->setQuantity($playerResource->getQuantity() + 1);
                            $this->entityManager->persist($playerResource);
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        $this->entityManager->flush();
    }

    /**
     * harvestPlayerQuarry : activates a player quarry, and gives him his resources selected
     * @param PlayerMYR $playerMYR
     * @param PheromonMYR $pheromoneMYR
     * @param ResourceMYR $resourceMYR
     * @return void
     * @throws Exception
     */
    public function harvestPlayerQuarry(PlayerMYR $playerMYR, PheromonMYR $pheromoneMYR,
                                        string $resourceMYR) : void
    {
        if($pheromoneMYR->isHarvested()) {
            throw new Exception("Quarry already harvested");
        }
        if(!($resourceMYR == MyrmesParameters::RESOURCE_TYPE_DIRT ||
            $resourceMYR == MyrmesParameters::RESOURCE_TYPE_STONE)) {
            throw new Exception("Invalid resource");
        }
        $tileType = $pheromoneMYR->getType();
        if($tileType->getType() != MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY) {
            throw new Exception("Invalid tile");
        }
        foreach ($playerMYR->getPersonalBoardMYR()->getPlayerResourceMYRs() as $playerResource) {
            if($playerResource->getResource()->getDescription() == $resourceMYR) {
                $pheromoneMYR->setHarvested(true);
                $this->entityManager->persist($pheromoneMYR);
                $playerResource->setQuantity($playerResource->getQuantity() + 1);
                $this->entityManager->persist($playerResource);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * harvestPlayerSubAnthill : activates all the player farms
     * @param PlayerMYR $playerMYR
     * @return void
     */
    public function harvestPlayerSubAnthill(PlayerMYR $playerMYR) : void
    {
        $playerPheromones = $playerMYR->getPheromonMYRs();
        foreach ($playerPheromones as $playerPheromone) {
            $tileType = $playerPheromone->getType();
            switch ($tileType->getType()) {
                case MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL:
                    $playerPheromone->setHarvested(true);
                    $this->entityManager->persist($playerPheromone);
                    $playerMYR->setScore($playerMYR->getScore() + 2);
                    $this->entityManager->persist($playerMYR);
                    break;
                default:
                    break;
            }
        }
        $this->entityManager->flush();
    }
}