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
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $season = new SeasonMYR();
        $season->setDiceResult(MyrmesParameters::BONUS_POINT);
        $season->setActualSeason(true);
        $mainBoard->addSeason($season);
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
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $season = new SeasonMYR();
        $season->setDiceResult(MyrmesParameters::BONUS_WARRIOR);
        $season->setActualSeason(true);
        $mainBoard->addSeason($season);
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
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $season = new SeasonMYR();
        $season->setDiceResult(MyrmesParameters::BONUS_WORKER);
        $mainBoard->addSeason($season);
        $season->setActualSeason(true);
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
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $season = new SeasonMYR();
        $season->setDiceResult(MyrmesParameters::BONUS_POINT);
        $season->setActualSeason(true);
        $mainBoard->addSeason($season);
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
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $season = new SeasonMYR();
        $season->setDiceResult(MyrmesParameters::PHASE_BIRTH);
        $season->setActualSeason(true);
        $mainBoard->addSeason($season);
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
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $season = new SeasonMYR();
        $season->setDiceResult(MyrmesParameters::BONUS_POINT);
        $season->setActualSeason(true);
        $mainBoard->addSeason($season);
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
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $season = new SeasonMYR();
        $season->setDiceResult(MyrmesParameters::BONUS_POINT);
        $season->setActualSeason(true);
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

}