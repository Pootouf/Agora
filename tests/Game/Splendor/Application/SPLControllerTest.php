<?php

namespace App\Tests\Game\Splendor\Application;

use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\GameUserRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Service\Game\SixQP\SixQPGameManagerService;
use App\Service\Game\SixQP\SixQPService;
use App\Service\Game\Splendor\SPLGameManagerService;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SPLControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private GameUserRepository $gameUserRepository;

    private PlayerSPLRepository $playerSPLRepository;
    private SPLGameManagerService $SPLGameManagerService;

    private GameSPLRepository $gameSPLRepository;
    private SPLService $SPLService;

    private TokenSPLService $tokenSPLService;
    public function testPlayersHaveAccessToGame() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $url = "/game/splendor/" . $gameId;
        //WHEN
        $this->client->request("GET", $url);
        //THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testIsSpectator() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->findOneById($gameId);
        $mainBoard = $game->getMainBoard();
        $token = $mainBoard->getTokens()[0];
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/" . $token->getId();
        $user3 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user3);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testWhenNotActivePlayerShouldReturnHTTPForbidden() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->findOneById($gameId);
        $mainBoard = $game->getMainBoard();
        $token = $mainBoard->getTokens()[0];
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/" . $token->getId();
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $player = $this->SPLService->getActivePlayer($game);
        $this->SPLService->endRoundOfPlayer($game, $player);
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testTakeTokenWhenPlayerIsFull() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->findOneById($gameId);
        $mainBoard = $game->getMainBoard();
        $tokens = $mainBoard->getTokens();
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $player = $this->SPLService->getActivePlayer($game);
        for ($i = 0; $i < 10; ++$i) {
            $player->getPersonalBoard()->addToken($tokens[$i]);
        }
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/10";
        $this->client->loginUser($user1);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse()->getStatusCode());
    }

    public function testTakeTokenWhenNotEnoughTokensOnMainBoard() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->findOneById($gameId);
        $mainBoard = $game->getMainBoard();
        $tokens = $mainBoard->getTokens();
        $whiteTokens = $this->tokenSPLService->getWhiteTokensFromCollection($tokens);
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        for ($i = 0; $i < 4; ++$i) {
            $mainBoard->removeToken($whiteTokens[$i]);
        }
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/4";
        $this->client->loginUser($user1);
        //WHEN
        $this->client->request("GET", $newUrl);
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/5";
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse()->getStatusCode());
    }
    public function testTakeTokenShouldReturnHTTPOK() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->findOneById($gameId);
        $mainBoard = $game->getMainBoard();
        $token = $mainBoard->getTokens()[0];
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/" . $token->getId();
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $player = $this->SPLService->getActivePlayer($game);
        $this->client->loginUser($user1);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
    }

    private function initializeGameWithTwoPlayers() : int
    {
        $this->client =  static::createClient();
        $this->SPLGameManagerService = static::getContainer()->get(SPLGameManagerService::class);
        $this->gameUserRepository = static::getContainer()->get(GameUserRepository::class);
        $this->gameSPLRepository = static::getContainer()->get(GameSPLRepository::class);
        $this->playerSPLRepository = static::getContainer()->get(PlayerSPLRepository::class);
        $this->tokenSPLService = static::getContainer()->get(TokenSPLService::class);
        $this->SPLService = static::getContainer()->get(SPLService::class);
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user1);
        $gameId = $this->SPLGameManagerService->createGame();
        $game = $this->gameSPLRepository->findOneById($gameId);
        $this->SPLGameManagerService->createPlayer("test0", $game);
        $this->SPLGameManagerService->createPlayer("test1", $game);
        try {
            $this->SPLGameManagerService->launchGame($game);
        } catch (\Exception $e) {
            $this->hasFailed();
        }
        return $gameId;
    }
}