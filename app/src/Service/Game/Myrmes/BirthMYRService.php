<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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
        $personalBoard = $player->getPersonalBoardMYR();
        for ($i = 1; $i < MyrmesParameters::$AREA_COUNT; $i += 1)
        {
            $nurses = $this->getNursesAtPosition($player ,$i);
            $nursesCount = $nurses->count();
            switch ($i)
            {
                case MyrmesParameters::$LARVAE_AREA:
                    $this->manageLarvae($player, $nursesCount);
                    break;
                case MyrmesParameters::$SOLDIERS_AREA:
                    $this->manageSoldiers($player, $nursesCount);
                    break;
                case MyrmesParameters::$WORKER_AREA:
                    $this->manageWorker($player, $nursesCount);
                    break;
                case MyrmesParameters::$WORKSHOP_ANTHILL_HOLE_AREA:
                    if ($nursesCount == 1)
                    {
                        $player->addAnthillHoleMYR(new AnthillHoleMYR());
                        $this->manageNursesAfterBonusGive(
                            $player, 1, MyrmesParameters::$WORKSHOP_ANTHILL_HOLE_AREA
                        );
                    }
                    break;
                case MyrmesParameters::$WORKSHOP_LEVEL_AREA:
                    if ($nursesCount == 1)
                    {
                        $level = $personalBoard->getAnthillLevel();
                        $personalBoard->setAnthillLevel($level + 1);
                        $this->manageNursesAfterBonusGive(
                            $player, 1, MyrmesParameters::$WORKSHOP_LEVEL_AREA
                        );
                    }
                    break;
                case MyrmesParameters::$WORKSHOP_NURSE_AREA:
                    if ($nursesCount == 1)
                    {
                        $nurse = new NurseMYR();
                        $nurse->setPosition(MyrmesParameters::$BASE_AREA);
                        $personalBoard->addNurse($nurse);
                        $this->manageNursesAfterBonusGive(
                            $player, 1, MyrmesParameters::$WORKSHOP_NURSE_AREA
                        );
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

        echo " nursesCount " . $nursesCount . " \n";
        if ($winLarvae != 0)
        {
            $personalBoard = $player->getPersonalBoardMYR();
            $personalBoard->setLarvaCount($personalBoard->getLarvaCount() + $winLarvae);
            $this->entityManager->persist($personalBoard);
            $this->manageNursesAfterBonusGive(
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

        if ($winSoldiers != 0)
        {
            $personalBoard = $player->getPersonalBoardMYR();
            $oldCountSoldiers = $personalBoard->getWarriorsCount();
            $newCountSoldiers = $oldCountSoldiers + $winSoldiers;
            $personalBoard->setWarriorsCount($newCountSoldiers);
            $this->entityManager->persist($personalBoard);
            $this->manageNursesAfterBonusGive(
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

        if ($winWorker != 0)
        {
            $personalBoard = $player->getPersonalBoardMYR();
            for ($count = 0; $count < $winWorker; $count++)
            {
                $worker = new AnthillWorkerMYR();
                $personalBoard->addAnthillWorker($worker);
                $this->entityManager->persist($worker);
                $this->entityManager->persist($personalBoard);
            }

            $this->manageNursesAfterBonusGive(
                $player,
                $nursesCount,
                MyrmesParameters::$WORKER_AREA
            );
        }
    }

    /**
     * manageNursesAfterBonusGive : Replace use nurses
     * @param PlayerMYR $player
     * @param int $nurseCount
     * @param int $positionOfNurse
     * @return void
     * @throws Exception
     */
    private function manageNursesAfterBonusGive(PlayerMYR $player, int $nurseCount, int $positionOfNurse) : void
    {
        if ($nurseCount > 0) {
            $nurses = $this->getNursesAtPosition($player, $positionOfNurse);
            foreach ($nurses as $n) {
                if ($nurseCount == 0) {
                    return;
                }
                switch ($positionOfNurse) {
                    case MyrmesParameters::$LARVAE_AREA:
                    case MyrmesParameters::$SOLDIERS_AREA:
                    case MyrmesParameters::$WORKER_AREA:
                    case MyrmesParameters::$WORKSHOP_ANTHILL_HOLE_AREA:
                    case MyrmesParameters::$WORKSHOP_LEVEL_AREA:
                    case MyrmesParameters::$WORKSHOP_NURSE_AREA:
                        $n->setPosition(MyrmesParameters::$BASE_AREA);
                        $this->entityManager->persist($n);
                        break;
                    case MyrmesParameters::$WORKSHOP_GOAL_AREA:
                        break;
                    default:
                        throw new Exception("Don't manage bonus");
                }
                $nurseCount--;
            }
        }
    }
}