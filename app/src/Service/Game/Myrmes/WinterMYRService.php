<?php

namespace App\Service\Game\Myrmes;


use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @codeCoverageIgnore
 */
class WinterMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly ResourceMYRRepository $resourceMYRRepository,
                                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository) {}


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

        if (!$pBoard->getPlayerResourceMYRs()->contains($playerResource))
        {
            throw new Exception("Resource don't belongs to the player");
        }

        $pBoard->removePlayerResourceMYR($playerResource);

        $this->entityManager->persist($pBoard);
        $this->entityManager->persist($playerResource);
        $this->entityManager->flush();
    }

    public function retrievePoints(PlayerMYR $player) : void
    {
        $game = $player->getGameMyr();
        $currentYear = $game->getMainBoardMYR()->getYearNum();
        $amountToSpend = $this->getAmountToSpend($player, $currentYear);
        $food = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $playerFood = $this->playerResourceMYRRepository->findOneBy(["resource" => $food]);
        $foodStock = $playerFood->getQuantity();
        $remaining = $amountToSpend - $foodStock;
        if ($remaining > 0) {
            $playerFood->setQuantity(0);
        } else {
            $playerFood->setQuantity($foodStock - $remaining);
        }
        $this->entityManager->persist($playerFood);
        for ($i = 0; $i < $remaining; ++$i) {
            $player->setScore($player->getScore() - 3);
        }
        $this->entityManager->persist($player);
        $this->entityManager->flush();
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