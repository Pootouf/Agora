<?php

namespace App\Tests\Game\SixQP\Application;

use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\GameUserRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\SixQP\SixQPGameManagerService;
use App\Service\Game\SixQP\SixQPService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SixQPControllerTest extends WebTestCase
{
    private SixQPGameManagerService $managerService;
    private GameUserRepository $gameUserRepository;

    private GameSixQPRepository $gameSixQPRepository;
    private PlayerSixQPRepository $playerSixQPRepository;

    private KernelBrowser $client1;
    private KernelBrowser $client2;

    private KernelBrowser $client3;

    private SixQPService $sixQPService;
    public function testPlayersHaveAccessToGame() : void
    {
        $gameId = $this->initializeGameWithTwoPlayers();
        $url = "/game/sixqp/" . $gameId;
        $this->client1->request("GET", $url);
        $this->assertResponseIsSuccessful();
        $this->client2->request("GET", $url);
        $this->assertResponseIsSuccessful();
    }
    public function testIsSpectator() : void
    {
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSixQPRepository->findOneById($gameId);
        $player = $game->getPlayerSixQPs()[0];
        $card = $player->getCards()[0];
        $newUrl = "/game/" . $gameId . "/sixqp/select/" . $card->getId();
        $user3 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client3->loginUser($user3);
        $this->client3->request("GET", $newUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client3->getResponse()->getStatusCode());
    }

    public function testCannotChooseTwoCards() : void
    {
        $gameId = $this->initializeGameWithTwoPlayers();
        $url = "/game/sixqp/" . $gameId;
        $this->client1->request("GET", $url);
        $game = $this->gameSixQPRepository->findOneBy(["id" => $gameId]);
        $player = $this->sixQPService->getPlayerFromNameAndGame($game, "test0");
        $card = $player->getCards()[0];
        $card2 = $player->getCards()[1];
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $this->client1->loginUser($user1);
        $newUrl = "/game/" . $gameId . "/sixqp/select/" . $card->getId();
        $this->client1->request("GET", $newUrl);
        $this->assertTrue($player->getCards()->contains($card));
        $this->assertEquals(Response::HTTP_OK,
            $this->client1->getResponse()->getStatusCode());
        $newUrl = "/game/" . $gameId . "/sixqp/select/" . $card2->getId();
        $this->client1->request("GET", $newUrl);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED,
            $this->client1->getResponse()->getStatusCode());
    }

    public function testCannotChooseCardIfNotOwned() : void
    {
        $gameId = $this->initializeGameWithTwoPlayers();
        $url = "/game/sixqp/" . $gameId;
        $this->client1->request("GET", $url);
        $player2 = $this->playerSixQPRepository->findOneBy(['game' => $gameId, 'username' => "test1"]);
        $card = $player2->getCards()[0];
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $this->client1->loginUser($user1);
        $newUrl = "/game/" . $gameId . "/sixqp/select/" . $card->getId();
        $this->client1->request("GET", $newUrl);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client1->getResponse()->getStatusCode());
    }

    public function testPlaceWithInvalidPlayer() : void
    {
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSixQPRepository->findOneById($gameId);
        $row = $game->getRowSixQPs()[0];
        $newUrl = "/game/" . $gameId . "/sixqp/place/row/" . $row->getId();
        $user3 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client3->loginUser($user3);
        $this->client3->request("GET", $newUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client3->getResponse()->getStatusCode());
    }

    public function testPlaceWithInvalidCard() : void
    {
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSixQPRepository->findOneById($gameId);
        $row = $game->getRowSixQPs()[0];
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $this->client1->loginUser($user1);
        $newUrl = "/game/" . $gameId . "/sixqp/place/row/" . $row->getId();
        $this->client1->request("GET", $newUrl);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED,
            $this->client1->getResponse()->getStatusCode());
    }
    private function initializeGameWithTwoPlayers() : int
    {
        $this->client1 =  static::createClient();
        $this->client2 = clone $this->client1;
        $this->client3 = clone $this->client1;
        $this->managerService = static::getContainer()->get(SixQPGameManagerService::class);
        $this->gameUserRepository = static::getContainer()->get(GameUserRepository::class);
        $this->gameSixQPRepository = static::getContainer()->get(GameSixQPRepository::class);
        $this->playerSixQPRepository = static::getContainer()->get(PlayerSixQPRepository::class);
        $this->sixQPService = static::getContainer()->get(SixQPService::class);
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $user3 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client1->loginUser($user1);
        $this->client2->loginUser($user2);
        $this->client3->loginUser($user3);
        $gameId = $this->managerService->createGame();
        $game = $this->gameSixQPRepository->findOneById($gameId);
        $this->managerService->createPlayer("test0", $game);
        $this->managerService->createPlayer("test1", $game);
        try {
            $this->managerService->launchGame($game);
        } catch (\Exception $e) {
        }
        return $gameId;
    }
}