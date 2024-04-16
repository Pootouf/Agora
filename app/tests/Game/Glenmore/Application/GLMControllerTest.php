<?php

namespace App\Tests\Game\Glenmore\Application;

use App\Entity\Game\DTO\Myrmes\BoardTileMYR;
use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\BuyingTileGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\GameUserRepository;
use App\Repository\Game\Glenmore\BoardTileGLMRepository;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileResourceGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Repository\Game\Glenmore\WarehouseLineGLMRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Service\Game\Glenmore\CardGLMService;
use App\Service\Game\Glenmore\DataManagementGLMService;
use App\Service\Game\Glenmore\GLMGameManagerService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use App\Service\Game\Glenmore\WarehouseGLMService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class GLMControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private GameUserRepository$gameUserRepository;

    private PlayerGLMRepository $playerGLMRepository;
    private GLMGameManagerService $GLMGameManagerService;

    private EntityManagerInterface $entityManager;
    private GameGLMRepository $gameGLMRepository;
    private GLMService $GLMService;
    private CardGLMService $cardGLMService;
    private DataManagementGLMService $dataManagementGLMService;
    private TileGLMService $tileGLMService;
    private WarehouseGLMService $warehouseGLMService;

    private WarehouseLineGLMRepository $warehouseLineGLMRepository;

    private TileGLMRepository $tileGLMRepository;

    private BoardTileGLMRepository $boardTileGLMRepository;

    private ResourceGLMRepository $resourceGLMRepository;

    private PlayerTileGLMRepository $playerTileGLMRepository;
    private PlayerTileResourceGLMRepository $playerTileResourceGLMRepository;

    public function testPlayersHaveAccessToGame(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $url = "/game/glenmore/" . $gameId;
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testIsSpectator() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/glenmore/" . $gameId;
        $user6 = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user6);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testHaveAccessToGameWhenIsInActivationPhase() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->setRoundPhase(GlenmoreParameters::$ACTIVATION_PHASE);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId;
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testPlayerSelectCardFromBoard(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $game->getPlayers()->first();
        $url = "/game/glenmore/" . $gameId . "/display/propertyCards/" . $player->getId();
        //WHEN
        $this->client->request("GET", $url);
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testSelectMoneyWarehouseProductionOnMainBoard(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneById($gameId);
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $url = "/game/glenmore/" . $gameId . "/select/money/warehouse/production/mainBoard/" . $lineId;
        //WHEN
        $this->client->request("GET", $url);
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testSelectResourceWarehouseProductionOnMainBoard(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneById($gameId);
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $url = "/game/glenmore/" . $gameId . "/select/resource/warehouse/production/mainBoard/" . $lineId;
        //WHEN
        $this->client->request("GET", $url);
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testBuyResourceWhenPlayerIsNotActive() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/buy/resource/warehouse/production/mainBoard/" . $lineId;
        $user4 = $this->gameUserRepository->findOneByUsername("test4");
        $this->client->loginUser($user4);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testBuyResourceWhenPlayerIsSpectator() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/buy/resource/warehouse/production/mainBoard/" . $lineId;
        $user6 = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user6);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }
    public function testBuyResourceWhenPlayerCannot() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/buy/resource/warehouse/production/mainBoard/" . $lineId;
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testBuyResourceWhenPlayerCan() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneById($gameId);
        $tileId = 17;
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => $tileId]);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $buyingTile = new BuyingTileGLM();
        $buyingTile->setCoordY(0);
        $buyingTile->setCoordX(0);
        $buyingTile->setPersonalBoardGLM($player->getPersonalBoard());
        $buyingTile->setBoardTile($boardTile);
        $this->entityManager->persist($buyingTile);
        $player->getPersonalBoard()->setBuyingTile($buyingTile);
        $this->entityManager->persist($player->getPersonalBoard());
        $warehouse = $game->getMainBoard()->getWarehouse();
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $warehouse->getId()]);
        $warehouse->addWarehouseLine($line);
        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();
        $lineId = $line->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/buy/resource/warehouse/production/mainBoard/" . $lineId;
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testActivateSellingResourceWarehouseProductionOnMainBoardWhenIsSpectator() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/activate/selling/resource/warehouse/production/mainBoard/" . $lineId;
        $user6 = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user6);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testActivateSellingResourceWarehouseProductionOnMainBoardWhenNotActive() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/activate/selling/resource/warehouse/production/mainBoard/" . $lineId;
        $user4 = $this->gameUserRepository->findOneByUsername("test4");
        $this->client->loginUser($user4);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testActivateSellingResourceWarehouseProductionOnMainBoardWhenPlayerActivatedResourceSelection() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->setActivatedResourceSelection(true);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/activate/selling/resource/warehouse/production/mainBoard/" . $lineId;
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testActivateSellingResourceWarehouseProductionOnMainBoard() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        /** @var WarehouseLineGLM $line */
        $line = $this->warehouseLineGLMRepository->findOneBy(["warehouseGLM" => $game->getMainBoard()->getWarehouse()->getId()]);
        $lineId = $line->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/activate/selling/resource/warehouse/production/mainBoard/" . $lineId;
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testSelectTileMainBoardWhenSpectator() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $tileId = 17;
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => $tileId]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId . "/select/tile/mainBoard/" . $boardTile->getId();
        $user6 = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user6);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }
    public function testSelectTileMainBoardWhenNotActive() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $tileId = 17;
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => $tileId]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId . "/select/tile/mainBoard/" . $boardTile->getId();
        $user4 = $this->gameUserRepository->findOneByUsername("test4");
        $this->client->loginUser($user4);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testSelectTileMainBoardWhenCantAssignTileToPlayer() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->getPersonalBoard()->setPlayerGLM($player);
        $this->entityManager->persist($player);
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => 1]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $buyingTile = new BuyingTileGLM();
        $buyingTile->setBoardTile($boardTile);
        $buyingTile->setPersonalBoardGLM($player->getPersonalBoard());
        $player->getPersonalBoard()->setBuyingTile($buyingTile);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId . "/select/tile/mainBoard/" . $boardTile->getId();
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testSelectTileMainBoardWhenTileHasNoBuyCost() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->getPersonalBoard()->setPlayerGLM($player);
        $this->entityManager->persist($player);
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => 1]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $player->getPersonalBoard()->setPlayerGLM($player);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId . "/select/tile/mainBoard/" . $boardTile->getId();
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testSelectTileMainBoardWhenTileHasBuyCost() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->getPersonalBoard()->setPlayerGLM($player);
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_GREEN]);
        /** @var PlayerTileGLM $village */
        $village = $this->playerTileGLMRepository->findOneBy(["personalBoard" => $player->getPersonalBoard()]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setPlayer($player);
        $playerTileResource->setQuantity(1);
        $this->entityManager->persist($playerTileResource);
        $village->addPlayerTileResource($playerTileResource);
        $player->addPlayerTileResourceGLM($playerTileResource);
        $this->entityManager->persist($player);
        $this->entityManager->persist($village);
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => 17]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $game->getMainBoard()->addBoardTile($boardTile);
        $this->entityManager->persist($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $player->getPersonalBoard()->setPlayerGLM($player);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId . "/select/tile/mainBoard/" . $boardTile->getId();
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testSelectTileMainBoardWhenNoExistentPlayer() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        /** @var PlayerTileGLM $village */
        $village = $this->playerTileGLMRepository->findOneBy(["personalBoard" => $player->getPersonalBoard()]);
        $newUrl = "/game/glenmore/" . $gameId . "/select/tile/personalBoard/" . $village->getId();
        $user6 = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user6);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testSelectTileMainBoardWhenExistentPlayer() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        /** @var PlayerTileGLM $village */
        $village = $this->playerTileGLMRepository->findOneBy(["personalBoard" => $player->getPersonalBoard()]);
        $newUrl = "/game/glenmore/" . $gameId . "/select/tile/personalBoard/" . $village->getId();
        $user6 = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user6);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testSelectLeaderWhenNoExistentPlayer() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/glenmore/" . $gameId . "/select/leader";
        $user = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testSelectLeaderWhenExistentPlayer() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/glenmore/" . $gameId . "/select/leader";
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testSelectLeaderWhenExistentPlayerAndBuyingPhase() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $newUrl = "/game/glenmore/" . $gameId . "/select/leader";
        /** @var PlayerGLM $player */
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->setRoundPhase(GlenmoreParameters::$BUYING_PHASE);
        $player->getPersonalBoard()->setPlayerGLM($player);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->persist($player);
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => 37]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $buyingTile = new BuyingTileGLM();
        $buyingTile->setBoardTile($boardTile);
        $buyingTile->setPersonalBoardGLM($player->getPersonalBoard());
        $buyingTile->setCoordY(0);
        $buyingTile->setCoordX(0);
        $this->entityManager->persist($buyingTile);
        $player->getPersonalBoard()->setBuyingTile($buyingTile);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testSelectLeaderWhenExistentPlayerAndBuyingPhaseButPlayerCannot() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $newUrl = "/game/glenmore/" . $gameId . "/select/leader";
        /** @var PlayerGLM $player */
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->setRoundPhase(GlenmoreParameters::$BUYING_PHASE);
        $player->getPersonalBoard()->setPlayerGLM($player);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->persist($player);
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => 37]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $buyingTile = new BuyingTileGLM();
        $buyingTile->setBoardTile($boardTile);
        $buyingTile->setPersonalBoardGLM($player->getPersonalBoard());
        $buyingTile->setCoordY(0);
        $buyingTile->setCoordX(0);
        $this->entityManager->persist($buyingTile);
        $player->getPersonalBoard()->setBuyingTile($buyingTile);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testRemoveVillagerWhenNoExistentPlayer() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $villagerResource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$VILLAGER_RESOURCE]);
        /** @var PlayerTileGLM $village */
        $village = $this->playerTileGLMRepository->findOneBy(["personalBoard" => $player->getPersonalBoard()]);
        $villager = $this->playerTileResourceGLMRepository->findOneBy(
            ["resource" => $villagerResource, "playerTileGLM" => $village]
        );
        $newUrl = "/game/glenmore/" . $gameId . "/remove/" . $village->getId() . "/villager/" . $villager->getId();
        $user = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testRemoveVillagerWhenIsSpectator() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $villagerResource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$VILLAGER_RESOURCE]);
        /** @var PlayerTileGLM $village */
        $village = $this->playerTileGLMRepository->findOneBy(["personalBoard" => $player->getPersonalBoard()]);
        $villager = $this->playerTileResourceGLMRepository->findOneBy(
            ["resource" => $villagerResource, "playerTileGLM" => $village]
        );
        $newUrl = "/game/glenmore/" . $gameId . "/remove/" . $village->getId() . "/villager/" . $villager->getId();
        $user = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testRemoveVillagerWhenCannot() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->getPersonalBoard()->setPlayerGLM($player);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $villagerResource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$VILLAGER_RESOURCE]);
        /** @var PlayerTileGLM $village */
        $village = $this->playerTileGLMRepository->findOneBy(["personalBoard" => $player->getPersonalBoard()]);
        $villager = $this->playerTileResourceGLMRepository->findOneBy(
            ["resource" => $villagerResource, "playerTileGLM" => $village]
        );
        $newUrl = "/game/glenmore/" . $gameId . "/remove/" . $village->getId() . "/villager/" . $villager->getId();
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testRemoveVillagerWhenCan() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->getPersonalBoard()->setPlayerGLM($player);
        $this->entityManager->persist($player->getPersonalBoard());
        $villagerResource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$VILLAGER_RESOURCE]);
        /** @var PlayerTileGLM $village */
        $village = $this->playerTileGLMRepository->findOneBy(["personalBoard" => $player->getPersonalBoard()]);
        /** @var PlayerTileResourceGLM $villager */
        $villager = $this->playerTileResourceGLMRepository->findOneBy(
            ["resource" => $villagerResource, "playerTileGLM" => $village]
        );
        $villager->setQuantity(2);
        $this->entityManager->persist($villager);
        $movementResource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$MOVEMENT_RESOURCE]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($movementResource);
        $playerTileResource->setPlayer($player);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($village);
        $this->entityManager->persist($playerTileResource);
        $village->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($village);
        $player->addPlayerTileResourceGLM($playerTileResource);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId . "/remove/" . $village->getId() . "/villager/" . $villager->getId();
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testCancelResourcesSelectionWhenPlayerIsNull() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/glenmore/" . $gameId . "/cancel/resources/selection";
        $user = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testCancelResourcesSelection() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/glenmore/" . $gameId . "/cancel/resources/selection";
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testEndPlayerRoundWhenPlayerIsNull() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/glenmore/" . $gameId . "/end/player/round";
        $user = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testEndPlayerRound() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneBy(["id" => $gameId]);
        $mainBoard = $game->getMainBoard();
        $mainBoard->setLastPosition(0);
        $mainBoard->setGameGLM($game);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId . "/end/player/round";
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testDisplayPersonalBoard() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $game = $this->gameGLMRepository->findOneBy(["id" => $gameId]);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $player->setRoundPhase(GlenmoreParameters::$ACTIVATION_PHASE);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $newUrl = "/game/glenmore/" . $gameId . "/displayPersonalBoard/" . $player->getId();
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testShowMainBoard() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/" . $gameId . "/glenmore/show/main/board";
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testCancelBuyingTileWhenPlayerIsNull() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/" . $gameId . "/glenmore/cancel/buying/tile";
        $user = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testCancelBuyingTile() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneBy(["id" => $gameId]);
        $tileId = 17;
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["id" => $tileId]);
        $player = $this->GLMService->getPlayerFromNameAndGame($game, "test0");
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setMainBoardGLM($game->getMainBoard());
        $boardTile->setPosition(0);
        $this->entityManager->persist($boardTile);
        $buyingTile = new BuyingTileGLM();
        $buyingTile->setCoordY(0);
        $buyingTile->setCoordX(0);
        $buyingTile->setPersonalBoardGLM($player->getPersonalBoard());
        $buyingTile->setBoardTile($boardTile);
        $this->entityManager->persist($buyingTile);
        $player->getPersonalBoard()->setBuyingTile($buyingTile);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $newUrl = "/game/" . $gameId . "/glenmore/cancel/buying/tile";
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testCancelActivatingTileWhenPlayerIsNull() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/" . $gameId . "/glenmore/cancel/activating/tile";
        $user = $this->gameUserRepository->findOneByUsername("test6");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testCancelActivatingTile() : void
    {
        // GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = "/game/" . $gameId . "/glenmore/cancel/activating/tile";
        $user = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user);
        // WHEN
        $this->client->request("GET", $newUrl);
        // THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    public function testValidateResourceSelectionWhenPlayerIsNull() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = 'game/glenmore/' . $gameId . '/validate/resources/selection';
        $user6 = $this->gameUserRepository->findOneByUsername("test5");
        $this->client->loginUser($user6);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testValidateResourceSelectionShouldReturnHTTPOK() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $newUrl = 'game/glenmore/' . $gameId . '/validate/resources/selection';
        $user0 = $this->gameUserRepository->findOneByUsername("test0");
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    private function initializeGameWithFivePlayers() : int
    {
        $this->client = static::createClient();
        $this->GLMGameManagerService = static::getContainer()->get(GLMGameManagerService::class);
        $this->gameUserRepository = static::getContainer()->get(GameUserRepository::class);
        $this->playerTileGLMRepository = static::getContainer()->get(PlayerTileGLMRepository::class);
        $this->playerGLMRepository = static::getContainer()->get(PlayerGLMRepository::class);
        $this->gameGLMRepository = static::getContainer()->get(GameGLMRepository::class);
        $this->resourceGLMRepository = static::getContainer()->get(ResourceGLMRepository::class);
        $this->playerTileResourceGLMRepository = static::getContainer()->get(PlayerTileResourceGLMRepository::class);
        $this->tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $this->boardTileGLMRepository = static::getContainer()->get(BoardTileGLMRepository::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->GLMService = static::getContainer()->get(GLMService::class);
        $this->cardGLMService = static::getContainer()->get(CardGLMService::class);
        $this->dataManagementGLMService = static::getContainer()->get(DataManagementGLMService::class);
        $this->tileGLMService = static::getContainer()->get(TileGLMService::class);
        $this->warehouseLineGLMRepository = static::getContainer()->get(WarehouseLineGLMRepository::class);
        $this->warehouseGLMService = static::getContainer()->get(WarehouseGLMService::class);
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user1);
        $gameId = $this->GLMGameManagerService->createGame();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $this->GLMGameManagerService->createPlayer("test0", $game);
        $this->GLMGameManagerService->createPlayer("test1", $game);
        $this->GLMGameManagerService->createPlayer("test2", $game);
        $this->GLMGameManagerService->createPlayer("test3", $game);
        $this->GLMGameManagerService->createPlayer("test4", $game);

        try {
            $this->GLMGameManagerService->launchGame($game);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return $gameId;
    }

}