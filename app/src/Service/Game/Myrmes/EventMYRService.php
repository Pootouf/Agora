<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\Collection;

class EventMYRService
{

    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * chooseBonus : the player spends larvae, if he owns the needed amount, to get chosen bonus
     * @param PlayerMYR $player
     * @param int       $bonus
     * @return void
     * @throws Exception
     */
    public function chooseBonus(PlayerMYR $player, int $bonus) : void
    {
        if ($bonus < MyrmesParameters::$BONUS_LEVEL || $bonus > MyrmesParameters::$BONUS_WORKER) {
            throw new Exception("Bonus does not exist");
        }
        $personalBoard = $player->getPersonalBoardMYR();
        $currentBonus = $personalBoard->getBonus();
        $neededLarvae = abs($bonus - $currentBonus);
        $larvaCount = $personalBoard->getLarvaCount();
        $newLarvaCount = $larvaCount - $neededLarvae;
        if ($newLarvaCount < 0) {
            throw new Exception("Player can't choose this bonus, not enough larvae owned");
        }
        $personalBoard->setBonus($bonus);
        $personalBoard->setLarvaCount($newLarvaCount);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }
}