<?php

namespace App\Service\Game\Myrmes;


use App\Entity\Game\DTO\Game;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class WinterMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly ResourceMYRRepository $resourceMYRRepository,
                                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository,
                                private readonly MYRService $MYRService) {}


    /**
     * mustDropResourcesForWinter : indicate if the player must drop resources during winter
     * @param PlayerMYR $playerMYR
     * @return bool
     */
    public function mustDropResourcesForWinter(PlayerMYR $playerMYR): bool
    {
        $personalBoard = $playerMYR->getPersonalBoardMYR();
        $anthillLevel = $personalBoard->getAnthillLevel();
        $totalResourcesCount = 0;
        foreach($personalBoard->getPlayerResourceMYRs() as $playerResourceMYR) {
            $totalResourcesCount += $playerResourceMYR->getQuantity();
        }
        return $playerMYR->getPhase() == MyrmesParameters::PHASE_WINTER and
            $anthillLevel < 2 ?
            $totalResourcesCount > MyrmesParameters::WAREHOUSE_LOCATIONS_AVAILABLE_ANTHILL_LEVEL_LESS_THAN_2
            : $totalResourcesCount > MyrmesParameters::WAREHOUSE_LOCATIONS_AVAILABLE_ANTHILL_LEVEL_AT_LEAST_2;
    }

    /**
     * canManageEndOfWinter : indicates if all players have thrown their resources for winter
     * @param GameMYR $gameMYR
     * @return bool
     */
    public function canManageEndOfWinter(GameMYR $gameMYR) : bool
    {
        foreach ($gameMYR->getPlayers() as $player) {
            if($this->mustDropResourcesForWinter($player)) {
                return false;
            }
        }
        return true;
    }

    /**
     * canSetPhaseToWinter : indicate if the game must begin the winter phase
     * @param GameMYR $gameMYR
     * @return bool
     */
    public function canSetPhaseToWinter(GameMYR $gameMYR): bool
    {
        return $this->MYRService->getActualSeason($gameMYR)->getName() == MyrmesParameters::FALL_SEASON_NAME;
    }

    /**
     * Remove one cube's resource belongs to the player
     * @param PlayerMYR $player
     * @param PlayerResourceMYR $playerResource
     * @return void
     * @throws Exception
     */
    public function removeCubeOfWarehouse(PlayerMYR $player, PlayerResourceMYR $playerResource) : void
    {
        $pBoard = $player->getPersonalBoardMYR();
        if ($playerResource->getPersonalBoard() !== $pBoard) {
            throw new Exception("Resource don't belongs to the player");
        }
        if ($playerResource->getQuantity() < 1) {
            throw new Exception("This resource is not in enough quantity to remove it ");
        }

        $playerResource->setQuantity($playerResource->getQuantity() -1);

        $this->entityManager->persist($playerResource);
        $this->entityManager->flush();
    }


    /**
     * retrievePoints : during winter season, retrieves food and points from the player
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    public function retrievePoints(PlayerMYR $player) : void
    {
        $game = $player->getGameMyr();
        $currentYear = $game->getMainBoardMYR()->getYearNum();
        $amountToSpend = $this->getAmountToSpend($player, $currentYear);
        $food = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $playerFood = $this->playerResourceMYRRepository->findOneBy(
            ["resource" => $food, "personalBoard" => $player->getPersonalBoardMYR()]
        );
        $foodStock = $playerFood->getQuantity();
        $remaining = $amountToSpend - $foodStock;
        $remainingFoodQuantity = $foodStock - $amountToSpend;
        if ($remainingFoodQuantity < 0) {
            $remainingFoodQuantity = 0;
        }
        $playerFood->setQuantity($remainingFoodQuantity);
        $this->entityManager->persist($playerFood);

        for ($i = 0; $i < $remaining; ++$i) {
            $availableLarvae = $this->MYRService->getAvailableLarvae($player);
            if ($availableLarvae >= 3) {
                $larvaCount = $player->getPersonalBoardMYR()->getLarvaCount();
                $player->getPersonalBoardMYR()->setLarvaCount($larvaCount - 3);
                $this->entityManager->persist($player->getPersonalBoardMYR());
                continue;
            }
            $player->setScore($player->getScore() - 3);
        }
        if ($player->getScore() < 0) {
            $player->setScore(0);
        }
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * manageEndOfWinter : retrieve points after every player disposed of their
     *              resources and manage the end of round
     * @param GameMYR $gameMYR
     * @return void
     * @throws Exception
     */
    public function manageEndOfWinter(GameMYR $gameMYR): void
    {
        if(!$this->canManageEndOfWinter($gameMYR)) {
            throw new Exception("All members have not disposed of their resources yet");
        }
        foreach($gameMYR->getPlayers() as $player) {
            $this->retrievePoints($player);
        }
        $this->MYRService->manageEndOfRound($gameMYR);
    }

    /**
     * getAmountToSpend : returns the amount of food a player must spend
     *
     * @param PlayerMYR $playerMYR
     * @param int       $year
     * @return int
     * @throws Exception
     */
    private function getAmountToSpend(PlayerMYR $playerMYR, int $year) : int
    {
        $warriorsCount = $playerMYR->getPersonalBoardMYR()->getWarriorsCount();
        return match ($year) {
            1 => 4 - $warriorsCount,
            2 => 5 - $warriorsCount,
            3 => 6 - $warriorsCount,
            default => throw new Exception("Year doesnt exist"),
        };
    }

}