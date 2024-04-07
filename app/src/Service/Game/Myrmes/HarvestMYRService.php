<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\TileMYR;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @codeCoverageIgnore
 */
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
        return $this->areAllPheromonesHarvested($playerMYR) && $playerMYR->getRemainingHarvestingBonus() > 0;
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

}