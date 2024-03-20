<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class BirthMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService,
                                private readonly NurseMYRRepository $nurseMYRRepository
    )
    {}

    /**
     * placeNurse : place the nurse in $newPosition
     * @param NurseMYR $nurseMYR
     * @param int $newPosition
     * @return void
     */
    public function placeNurse(NurseMYR $nurseMYR, int $newPosition) : void
    {
        $nurseMYR->setPosition($newPosition);
        $this->entityManager->persist($nurseMYR);
        $this->entityManager->flush();
    }

    /**
     * giveBirthBonus : give player birth bonus depending on nurses on areas
     * @param PlayerMYR $playerMYR
     * @return void
     * @throws \Exception
     */
    public function giveBirthBonus(PlayerMYR $player) : void
    {
        for($i = 1; $i < MyrmesParameters::$AREA_COUNT; $i += 1) {
            $nurses = $this->getNursesAtPosition($player ,$i);
            $nursesCount = $nurses->count();
            switch ($i) {
                case MyrmesParameters::$LARVAE_AREA;
                    $this->manageLarvae($player, $nursesCount);
                    break;
                case MyrmesParameters::$SOLDIERS_AREA;
                    $this->manageSoldiers($player, $nursesCount);
                    break;
                case MyrmesParameters::$WORKER_AREA:
                    $this->manageWorker($player, $nursesCount);
                    break;
                default:
                    throw new Exception("Don't give bonus");
            }
        }
    }

    /**
     * getNursesAtPosition : return nurses which is in $position
     * @param PlayerMYR $player
     * @param int $position
     * @return ArrayCollection
     */
    private function getNursesAtPosition(PlayerMYR $player, int $position): ArrayCollection
    {
        $nurses =  $this->nurseMYRRepository->findBy(["position" => $position,
            "player" => $player]);
        return new ArrayCollection($nurses);
    }

    private function getGainByCountNurse(array $gainsByCountNurse, int $countNurse) : int
    {
        $isWin = array_key_exists($countNurse, $gainsByCountNurse);
        return $isWin ? $gainsByCountNurse[$countNurse] : 0;
    }

    private function manageLarvae(PlayerMYR $player, int $nursesCount) : void
    {
        $winLarvae = $this->getGainByCountNurse(
            MyrmesParameters::$WIN_LARVAE_BY_NURSES_COUNT,
            $nursesCount
        );
        $player->setLarvaCount($player->getLarvaCount() + $winLarvae);
    }

    private function manageSoldiers(PlayerMYR $player, int $nursesCount) : void
    {
        $winSoldiers = $this->getGainByCountNurse(
            MyrmesParameters::$WIN_SOLDIERS_BY_NURSES_COUNT,
            $nursesCount
        );
        $personalBoard = $player->getPersonalBoardMYR();
        $oldCountSoldiers = $personalBoard->getWarriorsCount();
        $newCountSoldiers = $oldCountSoldiers + $winSoldiers;
        $player->getPersonalBoardMYR()->setWarriorsCount($newCountSoldiers);
    }

    private function manageWorker(PlayerMYR $player, int $nursesCount) : void
    {
        $winWorker = $this->getGainByCountNurse(
            MyrmesParameters::$WIN_WORKERS_BY_NURSES_COUNT,
            $nursesCount
        );

        $personalBoard = $player->getPersonalBoardMYR();

        for ($count = 0; $count < $winWorker; $count++)
        {
            $worker = new AnthillWorkerMYR();
            $personalBoard->addAnthillWorker($worker);
        }
    }

    private function manageNursesAfterBonusGive(PlayerMYR $player, int $nurseCount) : void
    {

    }
}