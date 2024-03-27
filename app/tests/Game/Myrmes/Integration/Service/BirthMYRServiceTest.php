<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Service\Game\Myrmes\BirthMYRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BirthMYRServiceTest extends KernelTestCase
{
     private EntityManagerInterface $entityManager;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testPlaceNurseWhenNurseIsAvailable()
    {
        // GIVEN
        $birthMYRService = static::getContainer()->get(BirthMYRService::class);
        $game = $this->createGame(2);
        $personalBoard = $game->getPlayers()->first()->getPersonalBoardMYR();
        $nurse = $personalBoard->getNurses()->first();
        $position = MyrmesParameters::$LARVAE_AREA;
        // WHEN
        $birthMYRService->placeNurse($nurse, $position);
        // THEN
        $this->assertEquals($position, $nurse->getPosition());
    }

    public function testPlaceNurseWhenNurseIsNotAvailable()
    {
        // GIVEN
        $birthMYRService = static::getContainer()->get(BirthMYRService::class);
        $game = $this->createGame(2);
        $personalBoard = $game->getPlayers()->first()->getPersonalBoardMYR();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setAvailable(false);
        $this->entityManager->persist($nurse);
        $this->entityManager->flush();
        $position = MyrmesParameters::$LARVAE_AREA;
        // THEN
        $this->expectException(\Exception::class);
        // THEN
        $birthMYRService->placeNurse($nurse, $position);
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
            $player->setColor("");
            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setLarvaCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setBonus(0);
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