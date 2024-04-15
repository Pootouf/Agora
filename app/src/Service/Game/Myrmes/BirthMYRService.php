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
     * @param PlayerMYR $player
     * @param int $area
     * @return void
     * @throws Exception
     */
    public function placeNurse(PlayerMYR $player, int $area) : void
    {
        if (!($area >= MyrmesParameters::BASE_AREA
            && $area < MyrmesParameters::AREA_COUNT)) {
            throw new Exception("Invalid area");
        }

        $howMuchAvailableNurse = $this->haveAvailableNurse($player);
        if ($howMuchAvailableNurse == 0) {
            throw new Exception("Don't have nurses");
        }

        $nursesAlreadyOnArea = $this->MYRService
            ->getNursesAtPosition($player, $area)->count();
        $howMuchNurseCanIPut = $this->howMuchNurseCanPlaceInArea(
            $area, $nursesAlreadyOnArea
        );
        if ($howMuchNurseCanIPut == 0
            || $howMuchAvailableNurse < $howMuchNurseCanIPut) {
            throw new Exception("Don't place nurses in area");
        }

        $personalBoard = $player->getPersonalBoardMYR();
        foreach ($personalBoard->getNurses() as $nurse)
        {
            if ($nurse->getArea() == MyrmesParameters::BASE_AREA) {
                $howMuchNurseCanIPut--;
                $nurse->setArea($area);
                $this->entityManager->persist($nurse);
            }
            if ($howMuchNurseCanIPut == 0) {
                break;
            }
        }

        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * giveBonusesFromEvent : get bonus from event phase
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    public function giveBonusesFromEvent(PlayerMYR $player) : void
    {
        $personalBoard = $player->getPersonalBoardMYR();
        switch ($personalBoard->getBonus())
        {
            case MyrmesParameters::BONUS_LARVAE:
                $oldLarvaCount = $personalBoard->getLarvaCount();
                $personalBoard->setLarvaCount($oldLarvaCount + 2);
                $this->entityManager->persist($personalBoard);
                break;
            case MyrmesParameters::BONUS_WARRIOR:
                $oldWarriorCount =  $personalBoard->getWarriorsCount();
                $personalBoard->setWarriorsCount($oldWarriorCount + 1);
                $this->entityManager->persist($personalBoard);
                break;
            case MyrmesParameters::BONUS_WORKER:
                $anthillWorker = new AnthillWorkerMYR();
                $anthillWorker->setWorkFloor(-1); // TODO use parameters
                $personalBoard->addAnthillWorker($anthillWorker);
                $this->entityManager->persist($anthillWorker);
                $this->entityManager->persist($personalBoard);
                break;
            default:
                throw new Exception("Don't give bonus");
        }
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
        for ($i = MyrmesParameters::LARVAE_AREA;
             $i <= MyrmesParameters::WORKER_AREA;
             $i += 1
        )
        {
            $nurses = $this->MYRService->getNursesAtPosition($player ,$i);
            $nursesCount = $nurses->count();
            switch ($i) {
                case MyrmesParameters::LARVAE_AREA:
                    $this->manageLarvae($player, $nursesCount);
                    break;
                case MyrmesParameters::SOLDIERS_AREA:
                    $this->manageSoldiers($player, $nursesCount);
                    break;
                case MyrmesParameters::WORKER_AREA:
                    $this->manageWorker($player, $nursesCount);
                    break;
            }
            $this->entityManager->flush();
        }
    }

    /**
     * cancelNursesPlacement : cancel the nurses placement on the birth tracks or workshop
     * @param PlayerMYR $playerMYR
     * @return void
     */
    public function cancelNursesPlacement(PlayerMYR $playerMYR): void
    {
        $nurses = $playerMYR->getPersonalBoardMYR()->getNurses();
        foreach($nurses as $nurse)
        {
            if ($nurse->isAvailable())
            {
                $nurse->setArea(MyrmesParameters::BASE_AREA);
                $this->entityManager->persist($nurse);
            }

        }
        $this->entityManager->persist($playerMYR->getPersonalBoardMYR());
        $this->entityManager->flush();
    }

    private function haveAvailableNurse(PlayerMYR $playerMYR) : int
    {
        $personalBoard = $playerMYR->getPersonalBoardMYR();

        $count = 0;
        foreach ($personalBoard->getNurses() as $nurse)
        {
            if ($nurse->isAvailable() &&
                $nurse->getArea() == MyrmesParameters::BASE_AREA)
            {
                $count += 1;
            }
        }

        return $count;
    }

    private function howMuchNurseCanPlaceInArea(int $area,
        int $nursesAlreadyOnArea) : int
    {

        $count = 0;
        $array = $this->getNeededNumberInArea($area);

        if ($nursesAlreadyOnArea == array_sum($array))
        {
            return 0;
        }

        if ($array != null) {
            foreach ($array as $nb) {
                if ($count == $nursesAlreadyOnArea) {
                    return $nb;
                } else {
                    $count += $nb;
                }
            }
        }

        return 0;
    }

    private function getGainByCountNurse(array $gainsByCountNurse, int $countNurse) : int
    {
        $isWin = array_key_exists($countNurse, $gainsByCountNurse);
        return $isWin ? $gainsByCountNurse[$countNurse] : 0;
    }

    private function getNeededNumberInArea(int $area) : array
    {
        $array = null;
        switch ($area) {
            case MyrmesParameters::LARVAE_AREA:
                $array = MyrmesParameters::NUMBER_NURSE_IN_LARVAE_AREA;
                break;
            case MyrmesParameters::WORKSHOP_AREA:
                $array = MyrmesParameters::NUMBER_NURSE_IN_WORKSHOP_AREA;
                break;
            case MyrmesParameters::SOLDIERS_AREA:
                $array = MyrmesParameters::NUMBER_NURSE_IN_SOLDIERS_AREA;
                break;
            case MyrmesParameters::WORKER_AREA:
                $array = MyrmesParameters::NUMBER_NURSE_IN_WORKER_AREA;
                break;
        }
        return $array;
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
            MyrmesParameters::WIN_LARVAE_BY_NURSES_COUNT,
            $nursesCount
        );

        if ($winLarvae == 0)
        {
            return;
        }

        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount($personalBoard->getLarvaCount() + $winLarvae);
        $this->entityManager->persist($personalBoard);


        $this->MYRService->manageNursesAfterBonusGive(
            $player,
            $nursesCount,
            MyrmesParameters::LARVAE_AREA
        );
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
            MyrmesParameters::WIN_SOLDIERS_BY_NURSES_COUNT,
            $nursesCount
        );

        if ($winSoldiers == 0)
        {
            return;
        }

        $personalBoard = $player->getPersonalBoardMYR();
        $oldCountSoldiers = $personalBoard->getWarriorsCount();
        $newCountSoldiers = $oldCountSoldiers + $winSoldiers;

        $personalBoard->setWarriorsCount($newCountSoldiers);

        $this->entityManager->persist($personalBoard);

        $this->MYRService->manageNursesAfterBonusGive(
                $player,
                $nursesCount,
                MyrmesParameters::SOLDIERS_AREA
        );
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
            MyrmesParameters::WIN_WORKERS_BY_NURSES_COUNT,
            $nursesCount
        );

        if ($winWorker == 0)
        {
            return;
        }

        $personalBoard = $player->getPersonalBoardMYR();

        for ($count = 0; $count < $winWorker; $count++)
        {
            $worker = new AnthillWorkerMYR();
            $worker->setWorkFloor(-1); // TODO use parameters
            $personalBoard->addAnthillWorker($worker);
            $this->entityManager->persist($worker);
        }

        $this->entityManager->persist($personalBoard);

        $this->MYRService->manageNursesAfterBonusGive(
            $player,
            $nursesCount,
            MyrmesParameters::WORKER_AREA
        );
    }
}