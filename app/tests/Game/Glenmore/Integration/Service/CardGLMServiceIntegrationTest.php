<?php

namespace App\Tests\Game\Glenmore\Integration\Service;

use App\Entity\Game\DTO\Card;
use App\Entity\Game\DTO\Game;
use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\CardGLM;
use App\Entity\Game\Glenmore\CreatedResourceGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerCardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Repository\Game\Glenmore\CardGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\CardGLMService;
use App\Service\Game\Glenmore\DataManagementGLMService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CardGLMServiceIntegrationTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;
    private CardGLMService $cardGLMService;

    private DataManagementGLMService $dataManagementGLMService;
    private ResourceGLMRepository $resourceGLMRepository;

    private CardGLMRepository $cardGLMRepository;
    private TileGLMRepository $tileGLMRepository;

    public function testApplyLochNessWhenNotOwned() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        //WHEN
        $result = $this->cardGLMService->applyLochNess($player->getPersonalBoard());
        //THEN
        $this->assertEmpty($result);
    }

    public function testApplyLochNessWhenOwnedAndNotUsed() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_NESS);
        $expectedSize = 1;
        //WHEN
        $result = $this->cardGLMService->applyLochNess($player->getPersonalBoard());
        //THEN
        $this->assertSame($expectedSize, $result->count());
    }

    public function testApplyLochNessWhenOwnedAndNotUsedWithManyPlayerTiles() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_NESS);
        $expectedSize = 1;
        for ($i = 1; $i <= 10; ++$i) {
            /** @var TileGLM $tile */
            $tile = $this->tileGLMRepository->findOneBy(["id" => $i]);
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tile);
            $playerTile->setCoordY($i + 15);
            $playerTile->setCoordX($i - 153);
            if ($i % 2 == 0) {
                $playerTile->setActivated(false);
                if (!$tile->getActivationBonus()->isEmpty()) {
                    ++$expectedSize;
                }
            } else {
                $playerTile->setActivated(true);
            }
            $playerTile->setPersonalBoard($player->getPersonalBoard());
            $this->entityManager->persist($playerTile);
            $player->getPersonalBoard()->addPlayerTile($playerTile);
            $this->entityManager->persist($player->getPersonalBoard());
        }
        $this->entityManager->flush();
        //WHEN
        $result = $this->cardGLMService->applyLochNess($player->getPersonalBoard());
        //THEN
        $this->assertSame($expectedSize, $result->count());
    }

    public function testApplyLochNessWhenOwnedAndUsed() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, true, GlenmoreParameters::$CARD_LOCH_NESS);
        //WHEN
        $result = $this->cardGLMService->applyLochNess($player->getPersonalBoard());
        //THEN
        $this->assertEmpty($result);
    }

    public function testClearCreatedResources() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $createdResource = new CreatedResourceGLM();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $createdResource->setQuantity(1);
        $createdResource->setPersonalBoardGLM($player->getPersonalBoard());
        $createdResource->setResource($resource);
        $this->entityManager->persist($createdResource);
        $player->getPersonalBoard()->addCreatedResource($createdResource);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        //WHEN
        $this->cardGLMService->clearCreatedResources($player);
        //THEN
        $this->assertEmpty($player->getPersonalBoard()->getCreatedResources());
    }

    public function testApplyCastleMoil() : void
    {
        $this->whiskyCardTests(GlenmoreParameters::$CARD_CASTLE_MOIL, 1);
    }

    public function testApplyDonanCastle() : void
    {
        $this->whiskyCardTests(GlenmoreParameters::$CARD_DONAN_CASTLE, 2);
    }

    public function testApplyArmadaleCastle() : void
    {
        //GIVEN
        $game = $this->createGame(5);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_ARMADALE_CASTLE);
        $expectedResult = $player->getPersonalBoard()->getMoney() + 3;
        //WHEN
        $this->cardGLMService->buyCardManagement($player->getPersonalBoard()->getPlayerTiles()->last());
        //THEN
        $this->assertSame($expectedResult, $player->getPersonalBoard()->getMoney());
    }

    public function testApplyLochLochy() : void
    {
        //GIVEN
        $game = $this->createGame(5);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_LOCHY);
        $expectedResult = -1;
        //WHEN
        $result = $this->cardGLMService->buyCardManagement($player->getPersonalBoard()->getPlayerTiles()->last());
        //THEN
        $this->assertSame($expectedResult,$result);
    }

    public function testSelectResourceForLochLochyWhenCreatedResourcesQuantityIsTwoButSameType() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        for ($i = 0; $i < 1; ++$i) {
            $createdResource = new CreatedResourceGLM();
            $createdResource->setResource($resource);
            $createdResource->setQuantity(2);
            $createdResource->setPersonalBoardGLM($player->getPersonalBoard());
            $this->entityManager->persist($createdResource);
            $player->getPersonalBoard()->addCreatedResource($createdResource);
            $this->entityManager->persist($createdResource);
        }
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->cardGLMService->selectResourceForLochLochy($player, $resource);
    }

    public function testSelectResourceForLochLochyWhenCreatedResourcesQuantityIsTwoAndDifferentType() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        for ($i = 1; $i <= 2; ++$i) {
            $resource = $this->resourceGLMRepository->findOneBy(["id" => $i]);
            $createdResource = new CreatedResourceGLM();
            $createdResource->setResource($resource);
            $createdResource->setQuantity(1);
            $createdResource->setPersonalBoardGLM($player->getPersonalBoard());
            $this->entityManager->persist($createdResource);
            $player->getPersonalBoard()->addCreatedResource($createdResource);
            $this->entityManager->persist($createdResource);
        }
        $this->entityManager->flush();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->cardGLMService->selectResourceForLochLochy($player, $resource);
    }

    public function testSelectResourceForLochLochyWhenCreatedResourcesIsSameColor() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $createdResource = new CreatedResourceGLM();
        $createdResource->setResource($resource);
        $createdResource->setQuantity(1);
        $createdResource->setPersonalBoardGLM($player->getPersonalBoard());
        $this->entityManager->persist($createdResource);
        $player->getPersonalBoard()->addCreatedResource($createdResource);
        $this->entityManager->persist($createdResource);
        $this->entityManager->flush();
        $expectedResult = 2;
        $expectedSize = 1;
        //WHEN
        $this->cardGLMService->selectResourceForLochLochy($player, $resource);
        //THEN
        $this->assertSame($expectedSize, $player->getPersonalBoard()->getCreatedResources()->count());
        $this->assertSame($expectedResult, $player->getPersonalBoard()->getCreatedResources()->first()->getQuantity());
    }

    public function testSelectResourceForLochLochyWhenCreatedResourcesIsAnotherColor() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $createdResource = new CreatedResourceGLM();
        $createdResource->setResource($resource);
        $createdResource->setQuantity(1);
        $createdResource->setPersonalBoardGLM($player->getPersonalBoard());
        $this->entityManager->persist($createdResource);
        $player->getPersonalBoard()->addCreatedResource($createdResource);
        $this->entityManager->persist($createdResource);
        $this->entityManager->flush();
        $newResource = $this->resourceGLMRepository->findOneBy(["id" => 2]);
        $expectedResult = 1;
        $expectedSize = 2;
        //WHEN
        $this->cardGLMService->selectResourceForLochLochy($player, $newResource);
        //THEN
        $this->assertSame($expectedSize, $player->getPersonalBoard()->getCreatedResources()->count());
        $this->assertSame($expectedResult, $player->getPersonalBoard()->getCreatedResources()->first()->getQuantity());
    }

    public function testSelectResourceForLochLochyWhenNoCreatedResource() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $newResource = $this->resourceGLMRepository->findOneBy(["id" => 2]);
        $expectedResult = 1;
        $expectedSize = 1;
        //WHEN
        $this->cardGLMService->selectResourceForLochLochy($player, $newResource);
        //THEN
        $this->assertSame($expectedSize, $player->getPersonalBoard()->getCreatedResources()->count());
        $this->assertSame($expectedResult, $player->getPersonalBoard()->getCreatedResources()->first()->getQuantity());
    }

    public function testValidateTakingOfResourcesForLochLochy() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_LOCHY);
        for ($i = 1; $i <= 2; ++$i) {
            $resource = $this->resourceGLMRepository->findOneBy(["id" => $i]);
            $createdResource = new CreatedResourceGLM();
            $createdResource->setResource($resource);
            $createdResource->setQuantity(1);
            $createdResource->setPersonalBoardGLM($player->getPersonalBoard());
            $this->entityManager->persist($createdResource);
            $player->getPersonalBoard()->addCreatedResource($createdResource);
            $this->entityManager->persist($createdResource);
        }
        $this->entityManager->flush();
        $expectedSizeOfLochyResources = 2;
        $expectedSizeOfCreated = 0;
        //WHEN
        $this->cardGLMService->validateTakingOfResourcesForLochLochy($player);
        //THEN
        $lochyTile = $player->getPersonalBoard()->getPlayerTiles()->last();
        $this->assertSame($expectedSizeOfCreated, $player->getPersonalBoard()->getCreatedResources()->count());
        $this->assertSame($expectedSizeOfLochyResources, $lochyTile->getPlayerTileResource()->count());
    }

    public function testApplyLochShielWhenNoProductionTile() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_SHIEL);
        $expectedNb = 0;
        //WHEN
        $this->cardGLMService->buyCardManagement($player->getPersonalBoard()->getPlayerTiles()->last());
        //THEN
        $result = $this->getProductionResourceNumber($player);
        $this->assertSame($expectedNb, $result);
    }

    public function testApplyLochShielWhenOneProductionTileButNotEmpty() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_SHIEL);
        $tile = $player->getPersonalBoard()->getPlayerTiles()->last();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($tile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $tile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($tile);
        $player->addPlayerTileResourceGLM($playerTileResource);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $expectedNb = 1;
        //WHEN
        $this->cardGLMService->buyCardManagement($player->getPersonalBoard()->getPlayerTiles()->last());
        //THEN
        $result = $this->getProductionResourceNumber($player);
        $this->assertSame($expectedNb, $result);
    }

    public function testApplyLochShielWhenOneProductionTileAndEmpty() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_SHIEL);
        $tile = $player->getPersonalBoard()->getPlayerTiles()->last();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(0);
        $playerTileResource->setPlayerTileGLM($tile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $tile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($tile);
        $player->addPlayerTileResourceGLM($playerTileResource);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $expectedNb = 1;
        //WHEN
        $this->cardGLMService->buyCardManagement($player->getPersonalBoard()->getPlayerTiles()->last());
        //THEN
        $result = $this->getProductionResourceNumber($player);
        $this->assertSame($expectedNb, $result);
    }

    public function testApplyLochShielWhenLochLochyEmpty() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_SHIEL);
        $lochShiel = $player->getPersonalBoard()->getPlayerTiles()->last();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_LOCHY);
        $lochLochy = $player->getPersonalBoard()->getPlayerTiles()->last();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(0);
        $playerTileResource->setPlayerTileGLM($lochLochy);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $lochLochy->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($lochLochy);
        $player->addPlayerTileResourceGLM($playerTileResource);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $expectedNb = 0;
        //WHEN
        $this->cardGLMService->buyCardManagement($lochShiel);
        //THEN
        $result = $this->getProductionResourceNumber($player);
        $this->assertSame($expectedNb, $result);
    }

    public function testApplyIonaAbbey() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_IONA_ABBEY);
        $expectedPoints = $this->giveExpectedPoints($player, 2, GlenmoreParameters::$TILE_TYPE_YELLOW);
        $this->entityManager->flush();
        //WHEN
        $this->cardGLMService->applyIonaAbbey($game);
        //THEN
        $this->assertSame($expectedPoints, $player->getScore());
    }

    public function testApplyLochMorar() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_LOCH_MORAR);
        $expectedPoints = $this->giveExpectedPoints($player, 2, GlenmoreParameters::$TILE_TYPE_GREEN) - 2;
        $this->entityManager->flush();
        //WHEN
        $this->cardGLMService->applyLochMorar($game);
        //THEN
        $this->assertSame($expectedPoints, $player->getScore());
    }

    public function testApplyDuartCastle() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, GlenmoreParameters::$CARD_DUART_CASTLE);
        $expectedPoints = $this->giveExpectedPoints($player, 3, GlenmoreParameters::$TILE_TYPE_VILLAGE);
        $this->entityManager->flush();
        //WHEN
        $this->cardGLMService->applyDuartCastle($game);
        //THEN
        $this->assertSame($expectedPoints, $player->getScore());
    }

    private function giveExpectedPoints(PlayerGLM $player, int $points, String $type) : int
    {
        $expectedPoints = $points;
        for ($i = 1; $i <= 30; ++$i) {
            /** @var TileGLM $tile */
            $tile = $this->tileGLMRepository->findOneBy(["id" => $i]);
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tile);
            $playerTile->setCoordY($i + 15);
            $playerTile->setCoordX($i - 153);
            if ($tile->getType() === $type) {
                $playerTile->setActivated(false);
                $expectedPoints += $points;
            }
            $playerTile->setPersonalBoard($player->getPersonalBoard());
            $this->entityManager->persist($playerTile);
            $player->getPersonalBoard()->addPlayerTile($playerTile);
            $this->entityManager->persist($player->getPersonalBoard());
        }
        return $expectedPoints;
    }

    private function getProductionResourceNumber(PlayerGLM $player) : int
    {
        $result = 0;
        foreach ($player->getPlayerTileResourceGLMs() as $playerTileResourceGLM) {
            if ($playerTileResourceGLM->getResource()->getType() === GlenmoreParameters::$PRODUCTION_RESOURCE) {
                $result += $playerTileResourceGLM->getQuantity();
            }
        }
        return $result;
    }

    private function whiskyCardTests(String $cardName, int $expectedResult) : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();
        $this->addCardAndTile($game, false, $cardName);
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->last();
        //WHEN
        $this->cardGLMService->buyCardManagement($playerTile);
        //THEN
        $result = $this->dataManagementGLMService->getWhiskyCount($player);
        $this->assertSame($expectedResult, $result);
    }

    private function addCardAndTile(GameGLM $game, bool $isActivated, String $cardName) : void
    {
        $player = $game->getPlayers()->first();
        /** @var CardGLM $card */
        $card = $this->cardGLMRepository->findOneBy(["name" => $cardName]);
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["card" => $card]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setCoordX(46);
        $playerTile->setCoordY(569);
        $playerTile->setActivated($isActivated);
        $playerTile->setPersonalBoard($player->getPersonalBoard());
        $this->entityManager->persist($playerTile);
        $player->getPersonalBoard()->addPlayerTile($playerTile);
        $playerCard = new PlayerCardGLM($player->getPersonalBoard(), $card);
        $this->entityManager->persist($playerCard);
        $player->getPersonalBoard()->addPlayerCardGLM($playerCard);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
    }

    private function createGame(int $nbOfPlayers) : GameGLM
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->cardGLMService = static::getContainer()->get(CardGLMService::class);
        $this->dataManagementGLMService = static::getContainer()->get(DataManagementGLMService::class);
        $this->resourceGLMRepository = static::getContainer()->get(ResourceGLMRepository::class);
        $this->cardGLMRepository = static::getContainer()->get(CardGLMRepository::class);
        $this->tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = new GameGLM();
        $game->setGameName(AbstractGameManagerService::$GLM_LABEL);
        $mainBoard = new MainBoardGLM();
        $mainBoard->setGameGLM($game);
        $tilesLevelZero = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_ZERO]);
        $tilesLevelOne = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_ONE]);
        $tilesLevelTwo = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_TWO]);
        $tilesLevelThree = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_THREE]);
        $drawArray = [$tilesLevelZero, $tilesLevelOne, $tilesLevelTwo, $tilesLevelThree];
        for ($i = GlenmoreParameters::$TILE_LEVEL_ZERO; $i <= GlenmoreParameters::$TILE_LEVEL_THREE; ++$i) {
            $draw = new DrawTilesGLM();
            $draw->setLevel($i);
            $draw->setMainBoardGLM($mainBoard);
            $tiles = $drawArray[$i];
            foreach ($tiles as $tile) {
                $draw->addTile($tile);
            }
            $this->entityManager->persist($draw);
            $mainBoard->addDrawTile($draw);
            $this->entityManager->persist($mainBoard);
            $warehouse = new WarehouseGLM();
            $warehouse->setMainBoardGLM($mainBoard);
            $this->entityManager->persist($warehouse);
            $this->entityManager->persist($mainBoard);
        }

        for ($i = 0; $i < $nbOfPlayers; $i++) {
            $player = new PlayerGLM('test', $game);
            $player->setRoundPhase(0);
            $player->setGameGLM($game);
            $player->setTurnOfPlayer(false);
            $player->setScore(0);
            $game->addPlayer($player);
            $personalBoard = new PersonalBoardGLM();
            $player->setPersonalBoard($personalBoard);
            $personalBoard->setPlayerGLM($player);
            $personalBoard->setLeaderCount(0);
            $personalBoard->setMoney(GlenmoreParameters::$START_MONEY);
            $pawn = new PawnGLM();
            $pawn->setColor(GlenmoreParameters::$COLOR_FROM_POSITION[$i]);
            $pawn->setPosition($i);
            $pawn->setMainBoardGLM($mainBoard);
            $player->setPawn($pawn);
            $this->entityManager->persist($pawn);
            $this->entityManager->persist($player);
            $startVillages = $this->tileGLMRepository->findBy(['name' => GlenmoreParameters::$TILE_NAME_START_VILLAGE]);
            $villager = $this->resourceGLMRepository->findOneBy(['type' => GlenmoreParameters::$VILLAGER_RESOURCE]);
            $playerTile = new PlayerTileGLM();
            $playerTile->setActivated(false);
            $playerTile->setCoordX(0);
            $playerTile->setCoordY(0);
            $playerTile->setTile($startVillages[$i]);
            $playerTile->setCoordX(0);
            $playerTile->setCoordY(0);
            $playerTile->setPersonalBoard($personalBoard);
            $personalBoard->addPlayerTile($playerTile);
            $this->entityManager->persist($playerTile);
            $playerTileResource = new PlayerTileResourceGLM();
            $playerTileResource->setPlayerTileGLM($playerTile);
            $playerTileResource->setPlayer($player);
            $playerTileResource->setResource($villager);
            $playerTileResource->setQuantity(1);
            $this->entityManager->persist($playerTileResource);
            $this->entityManager->persist($personalBoard);
            $this->entityManager->persist($player);
            $this->entityManager->flush();
        }

        for ($i = $nbOfPlayers; $i < GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD; ++$i) {
            $drawTiles = $mainBoard->getDrawTiles();
            $level = 0;
            for ($j = GlenmoreParameters::$TILE_LEVEL_ZERO; $j <= GlenmoreParameters::$TILE_LEVEL_THREE; ++$j) {
                if ($drawTiles->get($j)->getTiles()->isEmpty()) {
                    ++$level;
                } else {
                    break;
                }
            }
            $draw = $drawTiles->get($level);
            $tile = $draw->getTiles()->first();
            $mainBoardTile = new BoardTileGLM();
            $mainBoardTile->setTile($tile);
            $mainBoardTile->setMainBoardGLM($mainBoard);
            $mainBoardTile->setPosition($i);
            $this->entityManager->persist($mainBoardTile);
            $draw->removeTile($tile);
            $this->entityManager->persist($draw);
        }
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setTurnOfPlayer(true);
        $this->entityManager->persist($firstPlayer);
        $this->entityManager->flush();
        return $game;
    }
}