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
        $nurseMYR->setArea($newPosition);
        $this->entityManager->persist($nurseMYR);
        $this->entityManager->flush();
    }

    /**
     * giveBonusesFromEvent : get bonus from event phase
     * @param PlayerMYR $player
     * @return void
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
                $anthillWorker->setPlayer($player);
                $anthillWorker->setWorkFloor(-1); // TODO use parameters
                $personalBoard->addAnthillWorker($anthillWorker);
                $this->entityManager->persist($anthillWorker);
                $this->entityManager->persist($personalBoard);
                break;
            default:
                break;
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
        for ($i = 1; $i < MyrmesParameters::AREA_COUNT; $i += 1)
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
                default:
                    throw new Exception("Impossible give bonus");
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
        foreach($nurses as $nurse) {
            $nurse->setArea(MyrmesParameters::BASE_AREA);
            $this->entityManager->persist($nurse);
        }
        $this->entityManager->persist($playerMYR->getPersonalBoardMYR());
        $this->entityManager->flush();
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
            MyrmesParameters::WIN_LARVAE_BY_NURSES_COUNT,
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
                MyrmesParameters::LARVAE_AREA
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
            MyrmesParameters::WIN_SOLDIERS_BY_NURSES_COUNT,
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
                MyrmesParameters::SOLDIERS_AREA
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
            MyrmesParameters::WIN_WORKERS_BY_NURSES_COUNT,
            $nursesCount
        );

        $personalBoard = $player->getPersonalBoardMYR();

        for ($count = 0; $count < $winWorker; $count++)
        {
            $worker = new AnthillWorkerMYR();
            $worker->setPlayer($player);
            $worker->setWorkFloor(-1); // TODO use parameters

            $personalBoard->addAnthillWorker($worker);
            $this->entityManager->persist($worker);
        }

        $this->entityManager->persist($personalBoard);

        if ($winWorker != 0)
        {
            $this->MYRService->manageNursesAfterBonusGive(
                $player,
                $nursesCount,
                MyrmesParameters::WORKER_AREA
            );
        }
    }
}