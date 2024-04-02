<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @codeCoverageIgnore
 */
class WorkshopMYRService
{

    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService)
    {}

    /**
     * Manage resources and purchase about position of nurse
     * @param PlayerMYR $player
     * @param int $workshop
     * @return void
     * @throws Exception
     */
    public function manageWorkshop(PlayerMYR $player, int $workshop) {
        $nurses = $this->MYRService->getNursesAtPosition($player, MyrmesParameters::WORKSHOP_AREA);
        $nursesCount = $nurses->count();
        switch ($workshop) {
            case MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA:
                $this->manageAnthillHole($nursesCount, $player);
                break;
            case MyrmesParameters::WORKSHOP_LEVEL_AREA:
                $this->manageLevel($nursesCount, $player);
                break;
            case MyrmesParameters::WORKSHOP_NURSE_AREA:
                if ($this->canBuyNurse($player)) {
                    $this->manageNurse($nursesCount, $player);
                }
                break;
            case MyrmesParameters::WORKSHOP_GOAL_AREA:
                break;
            default:
                throw new Exception("Don't give bonus");
        }
        $this->entityManager->flush();
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
                $player, 1, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA
            );
        }
    }

    /**
     * Check if player can increase anthill level
     * @param PlayerMYR $player
     * @param array $requestResources
     * @return bool
     */
    private function canIncreaseLevel(PlayerMYR $player, array $requestResources) : bool
    {
        $pBoard = $player->getPersonalBoardMYR();

        $haveResource = array_fill_keys(array_keys($requestResources), 0);

        foreach ($pBoard->getPlayerResourceMYRs() as $playerResource)
        {
            $resource = $playerResource->getResource();
            if (array_key_exists($resource->getDescription(), $requestResources))
            {
                $haveResource[$resource->getDescription()]
                    = $playerResource->getQuantity();
            }
        }

        foreach (array_keys($haveResource) as $resource)
        {
            if ($haveResource[$resource] < $requestResources[$resource]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get buy about start anthill level
     * @param int $level
     * @return array
     * @throws Exception
     */
    private function getBuyForLevel(int $level) : array
    {
        return match ($level) {
            0 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_ONE,
            1 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_TWO,
            2 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_THREE,
            default => throw new Exception("Don't buy"),
        };
    }

    /**
     * Player spend resource necessary for add anthill level
     * @param PlayerMYR $player
     * @param string $resourceStr
     * @param int $count
     * @return void
     */
    private function spendResource(PlayerMYR $player, string $resourceStr, int $count) : void
    {
        $personalBoard = $player->getPersonalBoardMYR();

        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResource)
        {
            $resource = $playerResource->getResource();
            if ($resource->getDescription() === $resourceStr)
            {
                $oldQuantity = $playerResource->getQuantity();
                $playerResource->setQuantity($oldQuantity - $count);
                return;
            }
        }
    }

    /**
     * Manage all changes driven by level increase
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

            if (!$this->canIncreaseLevel($player, $buys)) {
                throw new Exception("Can't increase anthill level");
            }

            foreach (array_keys($buys) as $resource)
            {
                $this->spendResource($player, $resource, $buys[$resource]);
            }

            $level = $personalBoard->getAnthillLevel();
            $personalBoard->setAnthillLevel($level + 1);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::WORKSHOP_LEVEL_AREA
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
        $pBoard = $player->getPersonalBoardMYR();

        return $pBoard->getLarvaCount() >= 2
            && $pBoard->getPlayerResourceMYRs()->count() >= 2;
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
        $pBoard = $player->getPersonalBoardMYR();

        if ($nursesCount == 1)
        {
            $pBoard->setLarvaCount($pBoard->getLarvaCount() - 2);

            for ($i = $pBoard->getPlayerResourceMYRs()->count() - 3;
                 $i < $pBoard->getPlayerResourceMYRs()->count();
                 $i++) {
                $playerResource = $pBoard->getPlayerResourceMYRs()->get($i);
                $pBoard->removePlayerResourceMYR($playerResource);
            }

            $nurse = new NurseMYR();
            $nurse->setPosition(MyrmesParameters::BASE_AREA);
            $pBoard->addNurse($nurse);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::WORKSHOP_NURSE_AREA
            );
        }
    }
}