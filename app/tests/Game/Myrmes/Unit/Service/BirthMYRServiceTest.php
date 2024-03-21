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
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class BirthMYRServiceTest extends TestCase
{
    private BirthMYRService $birthMYRService;

    protected function setUp() : void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $nurseRepository = $this->createMock(NurseMYRRepository::class);
        $this->birthMYRService = new BirthMYRService($entityManager, $MYRService, $nurseRepository);
    }

    public function testPlaceNurseWhenNurseIsAvailable()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();
        $nurse = $personalBoard->getNurses()->first();
        $position = MyrmesParameters::$LARVAE_AREA;
        // WHEN
        $this->birthMYRService->placeNurse($nurse, $position);
        // THEN
        $this->assertEquals($position, $nurse->getPosition());
    }

    public function testPlaceNurseWhenNurseIsNotAvailable()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setAvailable(false);
        $position = MyrmesParameters::$LARVAE_AREA;
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->birthMYRService->placeNurse($nurse, $position);
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
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
            $player->setPersonalBoardMYR($personalBoard);
            for($j = 0; $j < MyrmesParameters::$START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $nurse->setAvailable(true);
                $personalBoard->addNurse($nurse);
            }
        }
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);

        return $game;
    }
}