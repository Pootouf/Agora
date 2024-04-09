<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Service\Game\Myrmes\BirthMYRService;
use App\Service\Game\Myrmes\MYRService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class BirthMYRServiceTest extends TestCase
{
    private BirthMYRService $birthMYRService;
    private MYRService $MYRService;

    protected function setUp() : void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->MYRService = $this->createMock(MYRService::class);
        $this->birthMYRService = new BirthMYRService($entityManager, $this->MYRService);
    }

    public function testPlaceNurseWhenNurseIsAvailable()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();
        $nurse = $personalBoard->getNurses()->first();
        $position = MyrmesParameters::LARVAE_AREA;
        // WHEN
        $this->birthMYRService->placeNurse($nurse, $position);
        // THEN
        $this->assertEquals($position, $nurse->getArea());
    }

    public function testPlaceNurseWhenNurseIsNotAvailable()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setAvailable(false);
        $position = MyrmesParameters::LARVAE_AREA;
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->birthMYRService->placeNurse($nurse, $position);
    }

    public function testGiveLarvaeBonusFromBirthPhase() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();
        $position = MyrmesParameters::LARVAE_AREA;
        $personalBoard->getNurses()->first()->setArea($position);
        $oldLarvaCount = $personalBoard->getLarvaCount();

        $array = new ArrayCollection();
        $array->add($personalBoard->getNurses()->first());
        $this->MYRService->method("getNursesAtPosition")->willReturn($array);

        // WHEN

        $this->birthMYRService->giveBirthBonus($firstPlayer);

        // THEN

        $this->assertEquals($oldLarvaCount + 1, $personalBoard->getLarvaCount());
    }

    public function testGiveSoldiersBonusFromBirthPhase() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();
        $position = MyrmesParameters::SOLDIERS_AREA;
        $oldSoldiersCount = $personalBoard->getWarriorsCount();

        $array = new ArrayCollection();
        $array->add($personalBoard->getNurses()->first());
        $array->add($personalBoard->getNurses()->get(1));

        foreach ($array as $a)
        {
            $a->setArea($position);
        }

        $this->MYRService->method("getNursesAtPosition")->willReturn($array);

        // WHEN

        $this->birthMYRService->giveBirthBonus($firstPlayer);

        // THEN

        $this->assertEquals($oldSoldiersCount + 1, $personalBoard->getWarriorsCount());
    }

    public function testGiveWorkerBonusFromBirthPhase() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();
        $position = MyrmesParameters::WORKER_AREA;
        $oldWorkerCount = $personalBoard->getAnthillWorkers()->count();

        $array = new ArrayCollection();
        $array->add($personalBoard->getNurses()->first());
        $array->add($personalBoard->getNurses()->get(1));

        foreach ($array as $a)
        {
            $a->setArea($position);
        }

        $this->MYRService->method("getNursesAtPosition")->willReturn($array);

        // WHEN

        $this->birthMYRService->giveBirthBonus($firstPlayer);

        // THEN

        $this->assertEquals($oldWorkerCount + 1,
            $personalBoard->getAnthillWorkers()->count());
    }

    public function testDontGiveBonusFromBirthPhaseWhenWithoutNurseWerePlaced() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();

        $oldLarvaeCount = $personalBoard->getLarvaCount();
        $oldSoldiersCount = $personalBoard->getWarriorsCount();
        $oldWorkerCount = $personalBoard->getAnthillWorkers()->count();

        $array = new ArrayCollection();
        $this->MYRService->method("getNursesAtPosition")->willReturn($array);

        // WHEN

        $this->birthMYRService->giveBirthBonus($firstPlayer);

        // THEN

        $this->assertEquals($oldLarvaeCount,
            $personalBoard->getLarvaCount());
        $this->assertEquals($oldSoldiersCount,
            $personalBoard->getWarriorsCount());
        $this->assertEquals($oldWorkerCount,
            $personalBoard->getAnthillWorkers()->count());
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
        if($numberOfPlayers < MyrmesParameters::MIN_NUMBER_OF_PLAYER ||
            $numberOfPlayers > MyrmesParameters::MAX_NUMBER_OF_PLAYER) {
            throw new \Exception("TOO MUCH PLAYERS ON CREATE GAME");
        }
        $game = new GameMYR();
        for ($i = 0; $i < $numberOfPlayers; $i += 1) {
            $player = new PlayerMYR('test', $game);
            $game->addPlayer($player);
            $player->setGameMyr($game);
            $personalBoard = new PersonalBoardMYR();
            $player->setPersonalBoardMYR($personalBoard);
            for($j = 0; $j < MyrmesParameters::START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $nurse->setAvailable(true);
                $personalBoard->addNurse($nurse);
            }
        }
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        return $game;
    }
}