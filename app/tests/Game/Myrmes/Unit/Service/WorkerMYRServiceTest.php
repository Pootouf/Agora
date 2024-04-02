<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Service\Game\Myrmes\MYRService;
use App\Service\Game\Myrmes\WorkerMYRService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class WorkerMYRServiceTest extends TestCase
{

    public function testPlaceAnthillHoleWhenPlaceIsAvailable()
    {
        // GIVEN
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $anthillHoleRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $anthillHoleRepository->method("findOneBy")->willReturn(null);
        $pheromonRepository = $this->createMock(PheromonMYRRepository::class);
        $pheromonRepository->method("findBy")->willReturn(array());
        $preyRepository = $this->createMock(PreyMYRRepository::class);
        $tileRepository = $this->createMock(TileMYRRepository::class);
        $playerResourceRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $resourceRepository = $this->createMock(ResourceMYRRepository::class);
        $workerMYRService = new WorkerMYRService($entityManager, $MYRService,
            $anthillHoleRepository, $pheromonRepository, $preyRepository,
            $tileRepository, $playerResourceRepository, $resourceRepository);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $player = $game->getPlayers()->first();
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
        // THEN
        $this->assertNotEmpty($player->getAnthillHoleMYRs());
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAnthillHole()
    {
        // GIVEN
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $anthillHoleRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $anthillHoleRepository->method("findOneBy")->willReturn(new AnthillHoleMYR());
        $pheromonRepository = $this->createMock(PheromonMYRRepository::class);
        $pheromonRepository->method("findBy")->willReturn(array());
        $preyRepository = $this->createMock(PreyMYRRepository::class);
        $tileRepository = $this->createMock(TileMYRRepository::class);
        $playerResourceRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $resourceRepository = $this->createMock(ResourceMYRRepository::class);
        $workerMYRService = new WorkerMYRService($entityManager, $MYRService,
            $anthillHoleRepository, $pheromonRepository, $preyRepository,
            $tileRepository, $playerResourceRepository, $resourceRepository);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseTileIsWater()
    {
        // GIVEN
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $anthillHoleRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $pheromonRepository = $this->createMock(PheromonMYRRepository::class);
        $preyRepository = $this->createMock(PreyMYRRepository::class);
        $tileRepository = $this->createMock(TileMYRRepository::class);
        $playerResourceRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $resourceRepository = $this->createMock(ResourceMYRRepository::class);
        $workerMYRService = new WorkerMYRService($entityManager, $MYRService,
            $anthillHoleRepository, $pheromonRepository, $preyRepository,
            $tileRepository, $playerResourceRepository, $resourceRepository);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::WATER_TILE_TYPE);
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAPheromone()
    {
        // GIVEN
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $anthillHoleRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $anthillHoleRepository->method("findOneBy")->willReturn(null);
        $pheromonRepository = $this->createMock(PheromonMYRRepository::class);
        $tile = new TileMYR();
        $pheromonTile = new PheromonTileMYR();
        $pheromonTile->setTile($tile);
        $pheromon = new PheromonMYR();
        $pheromon->addPheromonTile($pheromonTile);
        $pheromonRepository->method("findBy")->willReturn(array($pheromon));
        $preyRepository = $this->createMock(PreyMYRRepository::class);
        $tileRepository = $this->createMock(TileMYRRepository::class);
        $playerResourceRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $resourceRepository = $this->createMock(ResourceMYRRepository::class);
        $workerMYRService = new WorkerMYRService($entityManager, $MYRService,
            $anthillHoleRepository, $pheromonRepository, $preyRepository,
            $tileRepository, $playerResourceRepository, $resourceRepository);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
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
        }
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);

        return $game;
    }
}