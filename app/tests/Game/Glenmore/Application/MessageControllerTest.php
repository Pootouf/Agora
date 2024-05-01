<?php

namespace App\Tests\Game\Glenmore\Application;

use App\Entity\Game\Glenmore\GameGLM;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Platform\UserRepository;
use App\Service\Game\Glenmore\GLMGameManagerService;
use App\Service\Game\Glenmore\GLMService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MessageControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private GLMService $service;
    private UserRepository $userRepository;

    private GLMGameManagerService $GLMGameManagerService;

    private GameGLMRepository $gameGLMRepository;

    public function testSendMessage() : void
    {
        //GIVEN
        $gameId = $this->initializeGameWithFivePlayers();
        $authorUsername = "test0";
        $message = "test";
        /** @var GameGLM $game */
        $game = $this->gameGLMRepository->findOneBy(["id" => $gameId]);
        $player = $this->service->getPlayerFromNameAndGame($game, $authorUsername);
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
        $this->service = static::getContainer()->get(GLMService::class);
        $this->GLMGameManagerService = static::getContainer()->get(GLMGameManagerService::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->gameGLMRepository = static::getContainer()->get(GameGLMRepository::class);
        $user1 = $this->userRepository->findOneByUsername("test0");
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