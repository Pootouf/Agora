<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use App\Service\Game\Myrmes\MYRService;
use App\Service\Game\Myrmes\WinterMYRService;
use App\Service\Game\Myrmes\WorkshopMYRService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function Symfony\Component\String\s;

class WorkshopMYRServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private WorkshopMYRService $workshopMYRService;
    private TileMYRRepository $tileMYRRepository;

    private TileTypeMYRRepository $tileTypeMYRRepository;

    private PlayerResourceMYRRepository $playerResourceMYRRepository;

    private ResourceMYRRepository $resourceMYRRepository;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->workshopMYRService = static::getContainer()->get(WorkshopMYRService::class);
        $this->tileMYRRepository = static::getContainer()->get(TileMYRRepository::class);
        $this->tileTypeMYRRepository = static::getContainer()->get(TileTypeMYRRepository::class);
        $this->playerResourceMYRRepository = static::getContainer()->get(PlayerResourceMYRRepository::class);
        $this->resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
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

    public function testPlaceAnthillLevelShouldFailBecauseChosenTileIsOnWater() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 10, "coord_Y" => 11]);
        $this->entityManager->persist($nurse);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
    }

    public function testPlaceAnthillLevelShouldFailBecauseChosenTileIsOnPheromoneTile() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $pheromone = new PheromonMYR();
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $tileType = $this->tileTypeMYRRepository->findOneBy(
            ["type" => MyrmesParameters::PHEROMONE_TYPE_ZERO, "orientation" => 0]
        );
        $pheromone->setType($tileType);
        $pheromoneTile = new PheromonTileMYR();
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 12]);
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $this->entityManager->persist($nurse);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
    }

    public function testPlaceAnthillLevelShouldFailBecauseChosenTileIsOnAnthillHole() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 12]);
        $anthillHole = new AnthillHoleMYR();
        $anthillHole->setMainBoardMYR($player->getGameMyr()->getMainBoardMYR());
        $anthillHole->setPlayer($player);
        $anthillHole->setTile($tile);
        $player->addAnthillHoleMYR($anthillHole);
        $this->entityManager->persist($player);
        $this->entityManager->persist($anthillHole);
        $this->entityManager->persist($nurse);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
    }

    public function testPlaceAnthillLevelShouldFailBecauseChosenTileIsOnPrey() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 12]);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $prey->setType(MyrmesParameters::LADYBUG_TYPE);
        $prey->setPlayer($player);
        $prey->setMainBoardMYR($game->getMainBoardMYR());
        $this->entityManager->persist($player);
        $this->entityManager->persist($prey);
        $this->entityManager->persist($nurse);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
    }

    public function testPlaceAnthillLevelShouldFailBecauseChosenTileNotAroundPheromones() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 12]);
        $this->entityManager->persist($player);
        $this->entityManager->persist($nurse);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
    }

    public function testPlaceAnthillLevelShouldNotFailBecauseChosenTileAroundPheromones() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $nurse2 = $player->getPersonalBoardMYR()->getNurses()->last();
        $nurse2->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 12]);
        $pheromone = new PheromonMYR();
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $tileType = $this->tileTypeMYRRepository->findOneBy(
            ["type" => MyrmesParameters::PHEROMONE_TYPE_ZERO, "orientation" => 0]
        );
        $pheromone->setType($tileType);
        $pheromoneTile = new PheromonTileMYR();
        $tile2 = $this->tileMYRRepository->findOneBy(["coord_X" => 6, "coord_Y" => 11]);
        $pheromoneTile->setTile($tile2);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $this->entityManager->persist($player);
        $this->entityManager->persist($nurse);
        $this->entityManager->persist($nurse2);
        $this->entityManager->flush();
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testPlaceAnthillLevelShouldPlayerOwnOneDirt() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 12]);
        $pheromone = new PheromonMYR();
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $tileType = $this->tileTypeMYRRepository->findOneBy(
            ["type" => MyrmesParameters::PHEROMONE_TYPE_ZERO, "orientation" => 0]
        );
        $pheromone->setType($tileType);
        $pheromoneTile = new PheromonTileMYR();
        $tile2 = $this->tileMYRRepository->findOneBy(["coord_X" => 6, "coord_Y" => 11]);
        $pheromoneTile->setTile($tile2);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $this->entityManager->persist($player);
        $this->entityManager->persist($nurse);
        $this->entityManager->flush();
        $expectedAnthillHoleNb = 1;
        $dirtResource = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        $expectedPlayerDirtResourceNb = 1;
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
        //THEN
        $this->assertSame($expectedAnthillHoleNb, $player->getAnthillHoleMYRs()->count());
        /** @var PlayerResourceMYR $playerDirtResource */
        $playerDirtResource = $this->playerResourceMYRRepository->findOneBy(
            ["resource" => $dirtResource, "personalBoard" => $player->getPersonalBoardMYR()]
        );
        $this->assertSame($expectedPlayerDirtResourceNb, $playerDirtResource->getQuantity());
    }

    public function testPlaceAnthillLevelShouldPlayerOwnTwoDirt() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 12]);
        $pheromone = new PheromonMYR();
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $tileType = $this->tileTypeMYRRepository->findOneBy(
            ["type" => MyrmesParameters::PHEROMONE_TYPE_ZERO, "orientation" => 0]
        );
        $pheromone->setType($tileType);
        $pheromoneTile = new PheromonTileMYR();
        $tile2 = $this->tileMYRRepository->findOneBy(["coord_X" => 6, "coord_Y" => 11]);
        $pheromoneTile->setTile($tile2);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $dirtResource = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        $playerDirtResource = new PlayerResourceMYR();
        $playerDirtResource->setResource($dirtResource);
        $playerDirtResource->setQuantity(1);
        $playerDirtResource->setPersonalBoard($player->getPersonalBoardMYR());
        $this->entityManager->persist($playerDirtResource);
        $this->entityManager->persist($pheromone);
        $this->entityManager->persist($player);
        $this->entityManager->persist($nurse);
        $this->entityManager->flush();
        $expectedAnthillHoleNb = 1;
        $expectedPlayerDirtResourceNb = 2;
        //WHEN
        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA, $tile);
        //THEN
        $this->assertSame($expectedAnthillHoleNb, $player->getAnthillHoleMYRs()->count());
        /** @var PlayerResourceMYR $playerDirtResource */
        $playerDirtResource = $this->playerResourceMYRRepository->findOneBy(
            ["resource" => $dirtResource, "personalBoard" => $player->getPersonalBoardMYR()]
        );
        $this->assertSame($expectedPlayerDirtResourceNb, $playerDirtResource->getQuantity());
    }

    public function testGetAvailableAnthillPositions() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $tilePheromone1 = $this->tileMYRRepository->findOneBy(["coord_X" => 6, "coord_Y" => 15]);
        $tilePheromone2 = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 16]);
        $tileType = $this->tileTypeMYRRepository->findOneBy(
            ["type" => MyrmesParameters::PHEROMONE_TYPE_ZERO, "orientation" => 0]
        );
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tilePheromone1);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tilePheromone2);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $player->addPheromonMYR($pheromone);
        $this->entityManager->persist($player);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 8, "coord_Y" => 15]);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $prey->setType(MyrmesParameters::LADYBUG_TYPE);
        $prey->setPlayer($player);
        $prey->setMainBoardMYR($game->getMainBoardMYR());
        $this->entityManager->persist($player);
        $this->entityManager->persist($prey);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 18]);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $prey->setType(MyrmesParameters::LADYBUG_TYPE);
        $prey->setPlayer($player);
        $prey->setMainBoardMYR($game->getMainBoardMYR());
        $this->entityManager->persist($player);
        $this->entityManager->persist($prey);
        $this->entityManager->flush();
        $expectedTiles = new ArrayCollection();
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 5, "coord_Y" => 14]);
        $expectedTiles->add($tile);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 6, "coord_Y" => 13]);
        $expectedTiles->add($tile);
        $tile = $this->tileMYRRepository->findOneBy(["coord_X" => 7, "coord_Y" => 14]);
        $expectedTiles->add($tile);
        //WHEN
        $result = $this->workshopMYRService->getAvailableAnthillHolesPositions($player);
        //THEN
        $this->assertSame($expectedTiles->count(), $result->count());
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