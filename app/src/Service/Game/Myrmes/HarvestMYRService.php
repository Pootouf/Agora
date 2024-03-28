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
     * Manage resources and purchase about position of nurse
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
                    if ($this->canIncreaseLevel($player)) {
                        $this->manageAnthillHole($nursesCount, $player);
                    }
                    break;
                case MyrmesParameters::$WORKSHOP_LEVEL_AREA:
                    $this->manageLevel($nursesCount, $player);
                    break;
                case MyrmesParameters::$WORKSHOP_NURSE_AREA:
                    if ($this->canBuyNurse($player)) {
                        $this->manageNurse($nursesCount, $player);
                    }
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
     * Manage all change driven by add anthill hole
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

    private function canIncreaseLevel(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();

        $canIncrease = false;
        switch ($personalBoard->getAnthillLevel())
        {
            case 0:
            case 1:
            case 2:
            default:
                break;
        }

        return $canIncrease;
    }

    private function getBuyForLevel(int $level) : array
    {
        $buys = null;
        switch ($level)
        {
            case 0:
                $buys = MyrmesParameters::$BUY_RESOURCE_FOR_LEVEL_ONE;
                break;
            case 1:
                $buys = MyrmesParameters::$BUY_RESOURCE_FOR_LEVEL_TWO;
                break;
            case 2:
                $buys = MyrmesParameters::$BUY_RESOURCE_FOR_LEVEL_THREE;
                break;
            default:
                break;
        }

        return $buys;
    }

    /**
     * Manage all change driven by level increase
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

            $buys = $this->getBuyForLevel($personalBoard->getAnthillLevel());

            foreach (array_keys($buys) as $key)
            {
                $amount = $buys[$key];


            }

            $level = $personalBoard->getAnthillLevel();
            $personalBoard->setAnthillLevel($level + 1);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::$WORKSHOP_LEVEL_AREA
            );
        }
    }

    /**
     * Check if player can buy nurse
     * @param PlayerMYR $player
     * @return bool
     */
    private function canBuyNurse(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();

        return $personalBoard->getLarvaCount() >= 2;
    }

    /**
     * Manage all change driven by add nurse
     * @param int $nursesCount
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function manageNurse(int $nursesCount, PlayerMYR $player) : void
    {
        $personalBoard = $player->getPersonalBoardMYR();

        if ($nursesCount == 1)
        {
            $personalBoard->setLarvaCount($personalBoard->getLarvaCount() - 2);
            $nurse = new NurseMYR();
            $nurse->setPosition(MyrmesParameters::$BASE_AREA);
            $personalBoard->addNurse($nurse);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::$WORKSHOP_NURSE_AREA
            );
        }
    }

}