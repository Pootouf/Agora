<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
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
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(1);
        $personalBoard->setLarvaCount(2);
        $bonusWanted = 3;
        $expectedLarvaCount = 0;
        //WHEN
        $this->eventMYRService->chooseBonus($player, $bonusWanted);

        //THEN
        $this->assertSame($expectedLarvaCount, $personalBoard->getLarvaCount());
        $this->assertSame($bonusWanted, $personalBoard->getBonus());
    }

    public function testChooseBonusShouldWorkWithNegative() : void
    {
        //GIVEN
        $game = new GameMYR();
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(5);
        $personalBoard->setLarvaCount(4);
        $bonusWanted = 3;
        $expectedLarvaCount = 2;
        //WHEN
        $this->eventMYRService->chooseBonus($player, $bonusWanted);

        //THEN
        $this->assertSame($expectedLarvaCount, $personalBoard->getLarvaCount());
        $this->assertSame($bonusWanted, $personalBoard->getBonus());
    }

    public function testChooseBonusShouldFailBecauseBonusDoesntExist() : void
    {
        //GIVEN
        $game = new GameMYR();
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $personalBoard->setBonus(5);
        $personalBoard->setLarvaCount(1);
        $bonusWanted = 3;
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->eventMYRService->chooseBonus($player, $bonusWanted);
    }

    public function testChooseBonusShouldFailBecauseNotEnoughLarvae() : void
    {
        //GIVEN
        $game = new GameMYR();
        $player = new PlayerMYR("test", $game);
        $personalBoard = new PersonalBoardMYR();
        $player->setPersonalBoardMYR($personalBoard);
        $bonusWanted = 10;
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->eventMYRService->chooseBonus($player, $bonusWanted);
    }


}