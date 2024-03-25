<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Service\Game\Myrmes\EventMYRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventMYRServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private EventMYRService $eventMYRService;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->eventMYRService = static::getContainer()->get(EventMYRService::class);
    }

    public function testChooseBonusShouldWorkWithPositive() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(2);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
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
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(4);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
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
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $bonusWanted = 10;
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->eventMYRService->chooseBonus($player, $bonusWanted);
    }

    public function testChooseBonusShouldFailBecauseNotEnoughLarvae() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(2);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        $bonusWanted = 1;
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->eventMYRService->chooseBonus($player, $bonusWanted);
    }


    private function createGame(int $numberOfPlayers) : GameMYR
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        if($numberOfPlayers < MyrmesParameters::$MIN_NUMBER_OF_PLAYER ||
            $numberOfPlayers > MyrmesParameters::$MAX_NUMBER_OF_PLAYER) {
            throw new \Exception("TOO MUCH PLAYERS ON CREATE GAME");
        }
        $game = new GameMYR();
        for ($i = 0; $i < $numberOfPlayers; $i += 1) {
            $player = new PlayerMYR('test', $game);
            $game->addPlayer($player);
            $player->setGameMyr($game);
            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setLarvaCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setBonus(5);
            $player->setPersonalBoardMYR($personalBoard);
            $player->setScore(0);
            $player->setGoalLevel(0);
            for($j = 0; $j < MyrmesParameters::$START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $nurse->setPosition(-1);
                $nurse->setArea(MyrmesParameters::$LARVAE_AREA);
                $nurse->setAvailable(true);
                $nurse->setPlayer($player);
                $personalBoard->addNurse($nurse);
                $entityManager->persist($nurse);
            }
            $entityManager->persist($player);
            $entityManager->persist($personalBoard);
        }
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(0);
        $mainBoard->setGame($game);
        $season = new SeasonMYR();
        $season->setName("Spring");
        $season->setMainBoardMYR($mainBoard);
        $season->setDiceResult(1);
        $entityManager->persist($season);
        $mainBoard->setActualSeason($season);
        $game->setMainBoardMYR($mainBoard);
        $game->setGameName("test");
        $game->setLaunched(true);
        $entityManager->persist($mainBoard);
        $entityManager->persist($game);
        $entityManager->flush();
        return $game;
    }
}