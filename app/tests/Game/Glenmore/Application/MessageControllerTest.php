<?php

namespace App\Tests\Game\Glenmore\Application;

use App\Entity\Game\Glenmore\GameGLM;
use App\Repository\Game\GameUserRepository;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Service\Game\GameService;
use App\Service\Game\Glenmore\GLMGameManagerService;
use App\Service\Game\Glenmore\GLMService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MessageControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private GameGLMRepository $gameGLMRepository;

    private GameService $gameService;
    public function testSendMessage() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $authorUsername = "test0";
        $message = "test";
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneBy(["id" => $gameId]);
        $player = $this->gameService->getPlayerFromNameAndGame($game, $authorUsername);
        $url = "/game/" . $gameId . "/message/send/" . $player->getId() . "/" . $authorUsername . "/" . $message;
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testReceiveMessage() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $url = "/game/" . $gameId . "/message/display";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }


    private function initializeGameWithFivePlayers() : int
    {
        $this->client = static::createClient();
        $this->gameService = static::getContainer()->get(GameService::class);
        $GLMGameManagerService = static::getContainer()->get(GLMGameManagerService::class);
        $gameUserRepository = static::getContainer()->get(GameUserRepository::class);
        $this->gameGLMRepository = static::getContainer()->get(GameGLMRepository::class);
        $user1 = $gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user1);
        $gameId = $GLMGameManagerService->createGame();
        $game = $this->gameGLMRepository->findOneById($gameId);
        $GLMGameManagerService->createPlayer("test0", $game);
        $GLMGameManagerService->createPlayer("test1", $game);
        $GLMGameManagerService->createPlayer("test2", $game);
        $GLMGameManagerService->createPlayer("test3", $game);
        $GLMGameManagerService->createPlayer("test4", $game);

        try {
            $GLMGameManagerService->launchGame($game);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return $gameId;
    }
}