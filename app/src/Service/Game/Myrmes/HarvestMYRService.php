<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
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
                        throw new Exception("Pheromone already harvested");
                    }
                    $resource = $tile->getResource();
                    $tile->setResource(null);
                    $playerResources = $playerMYR->getPersonalBoardMYR()->getPlayerResourceMYRs();
                    foreach ($playerResources as $playerResource) {
                        if($playerResource === $resource) {
                            $playerResource->setQuantity($playerResource->getQuantity() + 1);
                            $playerPheromone->setHarvested(true);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    public function manageHarvest(PlayerMYR $player) {

        for ($i = 4; $i < MyrmesParameters::$AREA_COUNT; $i += 1)
        {
            $nurses = $this->MYRService->getNursesAtPosition($player ,$i);
            $nursesCount = $nurses->count();
            switch ($i) {
                case MyrmesParameters::$WORKSHOP_ANTHILL_HOLE_AREA:
                    $this->manageAnthillHole($nursesCount, $player);
                    break;
                case MyrmesParameters::$WORKSHOP_LEVEL_AREA:
                    $this->manageLevel($nursesCount, $player);
                    break;
                case MyrmesParameters::$WORKSHOP_NURSE_AREA:
                    $this->manageNurse($nursesCount, $player);
                    break;
                case MyrmesParameters::$WORKSHOP_GOAL_AREA:
                    break;
                default:
                    throw new Exception("Don't give bonus");
            }
            $this->entityManager->flush();
        }
    }

    /**
     * @param int $nursesCount
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function manageAnthillHole(int $nursesCount, PlayerMYR $player) : void
    {
        if ($nursesCount == 1)
        {
            $player->addAnthillHoleMYR(new AnthillHoleMYR());
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::$WORKSHOP_ANTHILL_HOLE_AREA
            );
        }
    }

    /**
     * @param int $nursesCount
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function manageLevel(int $nursesCount, PlayerMYR $player) : void
    {
        if ($nursesCount == 1)
        {
            $personalBoard = $player->getPersonalBoardMYR();
            $level = $personalBoard->getAnthillLevel();
            $personalBoard->setAnthillLevel($level + 1);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::$WORKSHOP_LEVEL_AREA
            );
        }
    }

    private function manageNurse(int $nursesCount, PlayerMYR $player) : void
    {

        $personalBoard = $player->getPersonalBoardMYR();

        if ($nursesCount == 1)
        {
            $nurse = new NurseMYR();
            $nurse->setPosition(MyrmesParameters::$BASE_AREA);
            $personalBoard->addNurse($nurse);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::$WORKSHOP_NURSE_AREA
            );
        }
    }

}