<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class BirthMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService)
    {}

    /**
     * placeNurse : place the nurse in $newPosition
     * @param NurseMYR $nurseMYR
     * @param int $newPosition
     * @throws Exception
     * @return void
     */
    public function placeNurse(NurseMYR $nurseMYR, int $newPosition) : void
    {
        if(!$nurseMYR->isAvailable()) {
            throw new Exception("NURSE NOT AVAILABLE");
        }
        $nurseMYR->setPosition($newPosition);
        $this->entityManager->persist($nurseMYR);
        $this->entityManager->flush();
    }

    /**
     * giveBirthBonus : give player birth bonus depending on nurses on areas
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    public function giveBirthBonus(PlayerMYR $player) : void
    {
        for ($i = 1; $i < MyrmesParameters::$AREA_COUNT; $i += 1)
        {
            $nurses = $this->MYRService->getNursesAtPosition($player ,$i);
            $nursesCount = $nurses->count();
            switch ($i) {
                case MyrmesParameters::$LARVAE_AREA:
                    $this->manageLarvae($player, $nursesCount);
                    break;
                case MyrmesParameters::$SOLDIERS_AREA:
                    $this->manageSoldiers($player, $nursesCount);
                    break;
                case MyrmesParameters::$WORKER_AREA:
                    $this->manageWorker($player, $nursesCount);
                    break;
                default:
                    break;
            }
            $this->entityManager->flush();
        }
    }

    private function getGainByCountNurse(array $gainsByCountNurse, int $countNurse) : int
    {
        $isWin = array_key_exists($countNurse, $gainsByCountNurse);
        return $isWin ? $gainsByCountNurse[$countNurse] : 0;
    }

    /**
     * manageLarvae : give larvae at player
     * @param PlayerMYR $player
     * @param int $nursesCount
     * @return void
     * @throws Exception
     */
    private function manageLarvae(PlayerMYR $player, int $nursesCount) : void
    {
        $winLarvae = $this->getGainByCountNurse(
            MyrmesParameters::$WIN_LARVAE_BY_NURSES_COUNT,
            $nursesCount
        );
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount($personalBoard->getLarvaCount() + $winLarvae);
        $this->entityManager->persist($personalBoard);
        if ($winLarvae != 0)
        {
            $this->MYRService->manageNursesAfterBonusGive(
                $player,
                $nursesCount,
                MyrmesParameters::$LARVAE_AREA
            );
        }
    }

    /**
     * manageSoldiers : give soldiers at player
     * @param PlayerMYR $player
     * @param int $nursesCount
     * @return void
     * @throws Exception
     */
    private function manageSoldiers(PlayerMYR $player, int $nursesCount) : void
    {
        $winSoldiers = $this->getGainByCountNurse(
            MyrmesParameters::$WIN_SOLDIERS_BY_NURSES_COUNT,
            $nursesCount
        );
        $personalBoard = $player->getPersonalBoardMYR();
        $oldCountSoldiers = $personalBoard->getWarriorsCount();
        $newCountSoldiers = $oldCountSoldiers + $winSoldiers;
        $personalBoard->setWarriorsCount($newCountSoldiers);
        $this->entityManager->persist($personalBoard);

        if ($winSoldiers != 0)
        {
            $this->MYRService->manageNursesAfterBonusGive(
                $player,
                $nursesCount,
                MyrmesParameters::$SOLDIERS_AREA
            );
        }
    }

    /**
     * manageWorker : give worker at player
     * @param PlayerMYR $player
     * @param int $nursesCount
     * @return void
     * @throws Exception
     */
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
            $this->entityManager->persist($worker);
            $this->entityManager->persist($personalBoard);
        }

        if ($winWorker != 0)
        {
            $this->MYRService->manageNursesAfterBonusGive(
                $player,
                $nursesCount,
                MyrmesParameters::$WORKER_AREA
            );
        }
    }
}