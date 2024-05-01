<?php

namespace App\Tests\Game\Splendor\Application;

use App\Repository\Game\Splendor\GameSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Repository\Platform\UserRepository;
use App\Service\Game\Splendor\SPLGameManagerService;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SPLControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;

    private PlayerSPLRepository $playerSPLRepository;
    private SPLGameManagerService $SPLGameManagerService;

    private EntityManagerInterface $entityManager;
    private GameSPLRepository $gameSPLRepository;
    private SPLService $SPLService;
    private TokenSPLService $tokenSPLService;

    public function testPlayersHaveAccessToGame(): void
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

    public function testPlayerSelectCardFromBoard(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $mainBoard = $game->getMainBoard();
        $card = $mainBoard->getRowsSPL()->first()->getDevelopmentCards()->first();
        $url = "/game/" . $gameId . "/splendor/select/board/" . $card->getId();
        //WHEN
        $this->client->request("GET", $url);
        $user = $this->userRepository->findOneBy(["username"=>"test0"]);
        $this->client->loginUser($user);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testPlayerSelectCardFromDraw(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $mainBoard = $game->getMainBoard();
        $level = $mainBoard->getDrawCards()->first()->getLevel();
        $url = "/game/" . $gameId . "/splendor/select/draw/" . $level;
        //WHEN
        $this->client->request("GET", $url);
        $user = $this->userRepository->findOneBy(["username"=>"test0"]);
        $this->client->loginUser($user);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testPlayerSelectCardFromPersonalBoard(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $player = $game->getPlayers()->first();
        $mainBoard = $game->getMainBoard();
        $card = $mainBoard->getRowsSPL()->first()->getDevelopmentCards()->first();
        $url = "/game/" . $gameId . "/splendor/reserve/card/" . $card->getId();
        $this->client->request("GET", $url);

        $card = $player->getPersonalBoard()->getPlayerCards()->first()->getDevelopmentCard();
        $url = "/game/" . $gameId . "/splendor/select/reserved/" . $card->getId();
        //WHEN
        $this->client->request("GET", $url);
        $user = $this->userRepository->findOneBy(["username"=>"test0"]);
        $this->client->loginUser($user);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testBuyCardWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $user3 = $this->userRepository->findOneBy(["username"=>"test2"]);
        $this->client->loginUser($user3);
        $mainBoard = $game->getMainBoard();
        $card = $mainBoard->getRowsSPL()->first()->getDevelopmentCards()->first();
        $url = "/game/" . $gameId . "/splendor/buy/card/" . $card->getId();
        //WHEN
        $this->client->request("GET", $url);
        //THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testBuyCardWhenNotActivePlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $user2 = $this->userRepository->findOneBy(["username"=>"test1"]);
        $this->client->loginUser($user2);
        $mainBoard = $game->getMainBoard();
        $card = $mainBoard->getRowsSPL()->first()->getDevelopmentCards()->first();
        $url = "/game/" . $gameId . "/splendor/buy/card/" . $card->getId();
        //WHEN
        $this->client->request("GET", $url);
        //THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testBuyCardWhenActivePlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $user2 = $this->userRepository->findOneBy(["username"=>"test0"]);
        $this->client->loginUser($user2);
        $mainBoard = $game->getMainBoard();
        $card = $mainBoard->getRowsSPL()->first()->getDevelopmentCards()->first();
        $url = "/game/" . $gameId . "/splendor/buy/card/" . $card->getId();
        //WHEN
        $this->client->request("GET", $url);
        //THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testBuyCardWhenActivePlayerAndCardReserved(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $mainBoard = $game->getMainBoard();
        $card = $mainBoard->getRowsSPL()->first()->getDevelopmentCards()->first();
        $url = "/game/" . $gameId . "/splendor/reserve/card/" . $card->getId();
        $user = $this->userRepository->findOneBy(["username"=>"test0"]);
        $this->client->loginUser($user);
        $this->client->request("GET", $url);
        $url = "/game/" . $gameId . "/splendor/buy/card/" . $card->getId();
        $user = $this->userRepository->findOneBy(["username"=>"test0"]);
        $this->client->loginUser($user);
        //WHEN
        $this->client->request("GET", $url);
        //THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testIsSpectator() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $mainBoard = $game->getMainBoard();
        $token = $mainBoard->getTokens()[0];
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/" . $token->getColor();
        $user3 = $this->userRepository->findOneBy(["username"=>"test2"]);
        $this->client->loginUser($user3);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testWhenNotActivePlayerShouldReturnHTTPForbidden(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $mainBoard = $game->getMainBoard();
        $token = $mainBoard->getTokens()[0];
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/" . $token->getColor();
        $user1 = $this->userRepository->findOneBy(["username"=>"test0"]);
        $player = $this->SPLService->getActivePlayer($game);
        $this->SPLService->endRoundOfPlayer($game, $player);
        $this->client->loginUser($user1);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testTakeTokenWhenPlayerIsFull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $mainBoard = $game->getMainBoard();
        $tokens = $mainBoard->getTokens();
        $user1 = $this->userRepository->findOneBy(["username"=>"test0"]);
        $player = $this->SPLService->getActivePlayer($game);
        for ($i = 0; $i < 10; ++$i) {
            $player->getPersonalBoard()->addToken($tokens[$i]);
        }
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/red";
        $this->client->loginUser($user1);
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testTakeTokenWhenMustSkipPlayerRound(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $mainBoard = $game->getMainBoard();
        $tokens = $mainBoard->getTokens();
        $user1 = $this->userRepository->findOneBy(["username"=>"test0"]);
        $player = $this->SPLService->getActivePlayer($game);
        for ($i = 0; $i < 9; ++$i) {
            $player->getPersonalBoard()->addToken($tokens[$i]);
            $this->entityManager->persist($player->getPersonalBoard());
            $this->entityManager->persist($player);
        }
        $this->entityManager->flush();
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/blue";
        $this->client->loginUser($user1);
        $this->client->request("GET", $newUrl);
        $this->assertEquals(Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode());
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/red";
        //WHEN
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testTakeTokenWhenNotEnoughTokensOnMainBoard(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $mainBoard = $game->getMainBoard();
        $tokens = $mainBoard->getTokens();
        $whiteTokens = $this->tokenSPLService->getWhiteTokensFromCollection($tokens);
        $user1 = $this->userRepository->findOneBy(["username"=>"test0"]);
        for ($i = 0; $i < 2; ++$i) {
            $mainBoard->removeToken($whiteTokens->first());
        }
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/white";
        $this->client->loginUser($user1);
        //WHEN
        $this->client->request("GET", $newUrl);
        $this->client->request("GET", $newUrl);
        //THEN
        $this->assertEquals(Response::HTTP_FORBIDDEN,
            $this->client->getResponse()->getStatusCode());
    }

    public function testTakeTokenShouldReturnHTTPOK(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameSPLRepository->find($gameId);
        $newUrl = "/game/" . $gameId . "/splendor/takeToken/red";
        $user1 = $this->userRepository->findOneBy(["username"=>"test0"]);
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
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->gameSPLRepository = static::getContainer()->get(GameSPLRepository::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->playerSPLRepository = static::getContainer()->get(PlayerSPLRepository::class);
        $this->tokenSPLService = static::getContainer()->get(TokenSPLService::class);
        $this->SPLService = static::getContainer()->get(SPLService::class);
        $user1 = $this->userRepository->findOneByUsername("test0");
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