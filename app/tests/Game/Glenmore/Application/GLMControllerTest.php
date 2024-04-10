<?php

namespace App\Tests\Game\Glenmore\Application;

use App\Entity\Game\DTO\Myrmes\BoardTileMYR;
use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\BuyingTileGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\GameUserRepository;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Repository\Game\Glenmore\WarehouseLineGLMRepository;
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
        $game = $this->gameGLMRepository->findOneById($gameId);
        $mainBoard = $game->getMainBoard();
        $tile = $mainBoard->getBoardTiles()->first();
        $tileId = $tile->getId();
        $newUrl = "/game/glenmore/" . $gameId . "/select/tile/mainBoard/" . $tileId;
        $user6 = $this->gameUserRepository->findOneByUsername("test5");
        $this->client->loginUser($user6);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
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
        $this->playerGLMRepository = static::getContainer()->get(PlayerGLMRepository::class);
        $this->gameGLMRepository = static::getContainer()->get(GameGLMRepository::class);
        $this->tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
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