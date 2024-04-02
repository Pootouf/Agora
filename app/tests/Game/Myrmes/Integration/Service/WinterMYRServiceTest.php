<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Service\Game\Myrmes\EventMYRService;
use App\Service\Game\Myrmes\WinterMYRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WinterMYRServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private WinterMYRService $winterMYRService;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->winterMYRService = static::getContainer()->get(WinterMYRService::class);
    }

    public function testRetrievePointsDuringYearOneAndNoWarriors() : void
    {
        //GIVEN
        $resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
        $playerResourceMYRRepository = static::getContainer()->get(PlayerResourceMYRRepository::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $initialScore = 25;
        $player->setScore($initialScore);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $expectedScore = 13;
        $expectedFood = 0;
        //WHEN
        $this->winterMYRService->retrievePoints($player);
        //THEN
        $food = $resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $playerFood = $playerResourceMYRRepository->findOneBy(["resource" => $food]);
        $playerFood = $playerFood != null ? $playerFood->getQuantity() : 0;
        $this->assertSame($expectedFood, $playerFood);
        $this->assertSame($expectedScore, $player->getScore());
    }

    public function testRetrievePointsDuringYearTwoAndNoWarriors() : void
    {
        //GIVEN
        $resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
        $playerResourceMYRRepository = static::getContainer()->get(PlayerResourceMYRRepository::class);
        $game = $this->createGame(2);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::SECOND_YEAR_NUM);
        $player = $game->getPlayers()->first();
        $initialScore = 25;
        $player->setScore($initialScore);
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $expectedScore = 10;
        $expectedFood = 0;
        //WHEN
        $this->winterMYRService->retrievePoints($player);
        //THEN
        $food = $resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $playerFood = $playerResourceMYRRepository->findOneBy(["resource" => $food]);
        $playerFood = $playerFood != null ? $playerFood->getQuantity() : 0;
        $this->assertSame($expectedFood, $playerFood);
        $this->assertSame($expectedScore, $player->getScore());
    }

    public function testRetrievePointsDuringYearThreeAndNoWarriors() : void
    {
        //GIVEN
        $resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
        $playerResourceMYRRepository = static::getContainer()->get(PlayerResourceMYRRepository::class);
        $game = $this->createGame(2);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::THIRD_YEAR_NUM);
        $player = $game->getPlayers()->first();
        $initialScore = 2;
        $player->setScore($initialScore);
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $expectedScore = 0;
        $expectedFood = 0;
        //WHEN
        $this->winterMYRService->retrievePoints($player);
        //THEN
        $food = $resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $playerFood = $playerResourceMYRRepository->findOneBy(["resource" => $food]);
        $playerFood = $playerFood != null ? $playerFood->getQuantity() : 0;
        $this->assertSame($expectedFood, $playerFood);
        $this->assertSame($expectedScore, $player->getScore());
    }

    public function testRetrievePointsDuringYearOneAndOneWarrior() : void
    {
        //GIVEN
        $resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
        $playerResourceMYRRepository = static::getContainer()->get(PlayerResourceMYRRepository::class);
        $game = $this->createGame(2);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::FIRST_YEAR_NUM);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setWarriorsCount(1);
        $initialScore = 10;
        $player->setScore($initialScore);
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->persist($player);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        $expectedScore = 1;
        $expectedFood = 0;
        //WHEN
        $this->winterMYRService->retrievePoints($player);
        //THEN
        $food = $resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $playerFood = $playerResourceMYRRepository->findOneBy(["resource" => $food]);
        $playerFood = $playerFood != null ? $playerFood->getQuantity() : 0;
        $this->assertSame($expectedFood, $playerFood);
        $this->assertSame($expectedScore, $player->getScore());
    }

    public function testRetrievePointsDuringNonExistingYear() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $game->getMainBoardMYR()->setYearNum(4);
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->winterMYRService->retrievePoints($player);
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        if($numberOfPlayers < MyrmesParameters::MIN_NUMBER_OF_PLAYER ||
            $numberOfPlayers > MyrmesParameters::MAX_NUMBER_OF_PLAYER) {
            throw new \Exception("TOO MUCH PLAYERS ON CREATE GAME");
        }
        $game = new GameMYR();
        for ($i = 0; $i < $numberOfPlayers; $i += 1) {
            $player = new PlayerMYR('test', $game);
            $game->addPlayer($player);
            $player->setGameMyr($game);
            $player->setColor("");
            $player->setPhase(MyrmesParameters::PHASE_EVENT);
            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setLarvaCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $personalBoard->setBonus(5);
            $player->setPersonalBoardMYR($personalBoard);
            $player->setScore(0);
            $player->setGoalLevel(0);
            $player->setRemainingHarvestingBonus(0);
            for($j = 0; $j < MyrmesParameters::START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $nurse->setPosition(-1);
                $nurse->setArea(MyrmesParameters::LARVAE_AREA);
                $nurse->setAvailable(true);
                $nurse->setPlayer($player);
                $personalBoard->addNurse($nurse);
                $entityManager->persist($nurse);
            }
            $entityManager->persist($player);
            $entityManager->persist($personalBoard);
        }
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(1);
        $mainBoard->setGame($game);
        $season = new SeasonMYR();
        $season->setName("Spring");
        $season->setDiceResult(1);
        $season->setMainBoard($mainBoard);
        $season->setActualSeason(true);
        $mainBoard->addSeason($season);
        $entityManager->persist($season);
        $entityManager->persist($season);
        $game->setMainBoardMYR($mainBoard);
        $game->setGameName("test");
        $game->setLaunched(true);
        $entityManager->persist($mainBoard);
        $entityManager->persist($game);
        $entityManager->flush();
        return $game;
    }
}