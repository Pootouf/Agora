<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Service\Game\Myrmes\EventMYRService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class EventMYRServiceTest extends TestCase
{
    private EventMYRService $eventMYRService;

    protected function setUp() : void
    {
        $entityManager = $this->createMock(EntityManager::class);
        $this->eventMYRService = new EventMYRService($entityManager);
    }

    public function testChooseBonusShouldWorkWithPositive() : void
    {
        //GIVEN
        $game = $this->initializeGameData(MyrmesParameters::BONUS_POINT);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(1);
        $personalBoard->setLarvaCount(2);
        $bonusWanted = 2;
        //WHEN
        $this->eventMYRService->upBonus($player);

        //THEN
        $this->assertSame($bonusWanted, $personalBoard->getBonus());
    }

    public function testChooseBonusShouldWorkWithNegative() : void
    {
        //GIVEN
        $game = $this->initializeGameData(MyrmesParameters::BONUS_WARRIOR);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(5);
        $personalBoard->setLarvaCount(4);
        $bonusWanted = 4;
        //WHEN
        $this->eventMYRService->lowerBonus($player);

        //THEN
        $this->assertSame($bonusWanted, $personalBoard->getBonus());
    }

    public function testChooseBonusShouldFailBecauseBonusDoesntExist() : void
    {
        //GIVEN
        $game = $this->initializeGameData(MyrmesParameters::BONUS_WORKER);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(MyrmesParameters::BONUS_WORKER);
        $personalBoard->setLarvaCount(1);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->eventMYRService->upBonus($player);
    }

    public function testChooseBonusShouldFailBecauseNotEnoughLarvae() : void
    {
        //GIVEN
        $game = $this->initializeGameData(MyrmesParameters::BONUS_POINT);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(MyrmesParameters::BONUS_POINT);
        $personalBoard->setLarvaCount(0);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->eventMYRService->upBonus($player);
    }

    public function testChooseBonusShouldFailBecauseNotEventPhase() : void
    {
        //GIVEN
        $game = $this->initializeGameData(MyrmesParameters::BONUS_POINT);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPhase(MyrmesParameters::PHASE_BIRTH);
        $player->setPersonalBoardMYR($personalBoard);
        $bonusWanted = 10;
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->eventMYRService->upBonus($player);
    }

    public function testUpBonusTwice() : void
    {
        //GIVEN
        $game = $this->initializeGameData(MyrmesParameters::BONUS_POINT);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(1);
        $personalBoard->setLarvaCount(2);
        $bonusWanted = 3;
        //WHEN
        $this->eventMYRService->upBonus($player);
        $this->eventMYRService->upBonus($player);

        //THEN
        $this->assertSame($bonusWanted, $personalBoard->getBonus());
    }

    public function testUpBonusThenLowerBonus() : void
    {
        //GIVEN
        $game = $this->initializeGameData(MyrmesParameters::BONUS_POINT);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(1);
        $personalBoard->setLarvaCount(2);
        $bonusWanted = 1;
        $selectedLarvaeExpected = 0;
        //WHEN
        $this->eventMYRService->upBonus($player);
        $this->eventMYRService->lowerBonus($player);

        //THEN
        $this->assertSame($bonusWanted, $personalBoard->getBonus());
        $this->assertSame($selectedLarvaeExpected, $personalBoard->getSelectedEventLarvaeAmount());

    }

    public function testConfirmBonus() : void
    {
        // GIVEN

        $game = $this->initializeGameData(MyrmesParameters::BONUS_POINT);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $player->getPersonalBoardMYR()->setBonus(
            MyrmesParameters::BONUS_HARVEST);

        // WHEN

        $this->eventMYRService->confirmBonus($player);

        // THEN

        $this->assertSame(3, $player->getRemainingHarvestingBonus());
    }

    public function testLowerBonusWhenPhaseIsNotEqualToEventPhase() : void
    {
        // GIVEN

        $game = $this->initializeGameData(MyrmesParameters::BONUS_POINT);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $player->getPersonalBoardMYR()->setBonus(
            MyrmesParameters::BONUS_HARVEST);
        $player->setPhase(MyrmesParameters::PHASE_INVALID);

        // THEN

        $this->expectException(\Exception::class);

        // WHEN

        $this->eventMYRService->lowerBonus($player);
    }

    public function testLowerBonusWhenCanNotLower() : void
    {
        // GIVEN

        $game = $this->initializeGameData(MyrmesParameters::BONUS_POINT);
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $player->getPersonalBoardMYR()->setBonus(0);
        $player->setPhase(MyrmesParameters::PHASE_EVENT);

        // THEN

        $this->expectException(\Exception::class);

        // WHEN

        $this->eventMYRService->lowerBonus($player);
    }

    public function testUpperBonusWhen() : void
    {
        // GIVEN

        $game = $this->initializeGameData(MyrmesParameters::BONUS_WORKER);

        $personalBoard = new PersonalBoardMYR();
        $personalBoard->setBonus(MyrmesParameters::BONUS_WARRIOR);
        $personalBoard->setLarvaCount(2);
        $personalBoard->setSelectedEventLarvaeAmount(1);
        $bonusExpected = MyrmesParameters::BONUS_PHEROMONE;

        $player = new PlayerMYR("test", $game);
        $player->setPersonalBoardMYR($personalBoard);

        // WHEN

        $this->eventMYRService->upBonus($player);

        // THEN

        $this->assertSame($bonusExpected, $personalBoard->getBonus());
        $this->assertSame(0, $personalBoard->getSelectedEventLarvaeAmount());
    }

    private function initializeGameData(int $bonus): GameMYR
    {
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        $season = new SeasonMYR();
        $season->setDiceResult($bonus);
        $season->setActualSeason(true);
        $mainBoard->addSeason($season);
        return $game;
    }

}