<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Service\Game\Myrmes\MYRService;
use App\Service\Game\Myrmes\WinterMYRService;
use App\Service\Game\Myrmes\WorkshopMYRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WorkshopMYRServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private WorkshopMYRService $workshopMYRService;
    private TileMYRRepository $tileMYRRepository;


    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->workshopMYRService = static::getContainer()->get(WorkshopMYRService::class);
        $this->tileMYRRepository = static::getContainer()->get(TileMYRRepository::class);
    }

    public function testManageWorkshopShouldFailBecauseNotGoodPhase() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_HARVEST);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, 45698);
    }

    public function testManageWorkshopShouldFailBecauseNoNurseInPosition() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, 45698);
    }

    public function testManageAnthillHoleShouldFailBecausePlayerReachedHisLimitOfAnthillHoles() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 12]);
        for ($i = 0; $i < MyrmesParameters::MAX_ANTHILL_HOLE_NB; ++$i) {
            $anthillHole = new AnthillHoleMYR();
            $anthillHole->setMainBoardMYR($player->getGameMyr()->getMainBoardMYR());
            $anthillHole->setPlayer($player);
            $anthillHole->setTile($tile);
            $this->entityManager->persist($anthillHole);
            $player->addAnthillHoleMYR($anthillHole);
            $this->entityManager->persist($player);
        }
        $this->entityManager->persist($nurse);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
        $game = new GameMYR();
        for ($i = 0; $i < $numberOfPlayers; $i += 1) {
            $player = new PlayerMYR('test', $game);
            $game->addPlayer($player);
            $player->setGameMyr($game);
            $player->setColor("");
            $player->setPhase(MyrmesParameters::PHASE_EVENT);
            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setLarvaCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setBonus(0);
            $nurse = new NurseMYR();
            $nurse->setPlayer($player);
            $nurse->setArea(MyrmesParameters::BASE_AREA);
            $nurse->setAvailable(true);
            $nurse->setPersonalBoardMYR($personalBoard);
            $this->entityManager->persist($nurse);
            $personalBoard->addNurse($nurse);
            $player->setPersonalBoardMYR($personalBoard);
            $player->setScore(0);
            $player->setGoalLevel(0);
            $player->setRemainingHarvestingBonus(0);
            $this->entityManager->persist($player);
            $this->entityManager->persist($personalBoard);
        }
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(0);
        $mainBoard->setGame($game);
        $season = new SeasonMYR();
        $season->setName("Spring");
        $season->setDiceResult(1);
        $season->setActualSeason(true);
        $season->setMainBoard($mainBoard);
        $mainBoard->addSeason($season);
        $this->entityManager->persist($season);
        $game->setMainBoardMYR($mainBoard);
        $game->setGameName("test");
        $game->setLaunched(true);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return $game;
    }
}