<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PlayerMYR;
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
                        if($playerResource === $resource) {
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
     * harvestPlayerSpecialTiles : activates all the special tiles of the player and gives him his bonus
     * @param PlayerMYR $playerMYR
     * @return void
     */
    public function harvestPlayerSpecialTiles(PlayerMYR $playerMYR) : void
    {
        $playerPheromones = $playerMYR->getPheromonMYRs();
        foreach ($playerPheromones as $playerPheromone) {
            $tileType = $playerPheromone->getType();
            switch ($tileType->getType()) {
                case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
                case MyrmesParameters::SPECIAL_TILE_STONE_FARM:
                    foreach ($playerMYR->getPersonalBoardMYR()->getPlayerResourceMYRs() as $playerResource) {
                        if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS) {
                            $playerResource->setQuantity($playerResource->getQuantity() + 1);
                            $this->entityManager->persist($playerResource);
                        }
                    }
                    break;
                case MyrmesParameters::SPECIAL_TILE_DIRT_QUARRY:
                case MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY:
                foreach ($playerMYR->getPersonalBoardMYR()->getPlayerResourceMYRs() as $playerResource) {
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_DIRT ||
                        $playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE) {
                        $playerResource->setQuantity($playerResource->getQuantity() + 1);
                        $this->entityManager->persist($playerResource);
                    }
                }
                break;
                case MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL:
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