<?php

namespace App\Tests\Game\Myrmes\Application;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Repository\Game\GameUserRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Service\Game\Myrmes\MYRGameManagerService;
use App\Service\Game\Myrmes\MYRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Translation\t;

class MYRControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private GameUserRepository $gameUserRepository;

    private ResourceMYRRepository $resourceMYRRepository;
    private PlayerMYRRepository $playerMYRRepository;
    private MYRGameManagerService $MYRGameManagerService;

    private EntityManagerInterface $entityManager;
    private GameMYRRepository $gameMYRRepository;
    private MYRService $MYRService;

    public function testPlayersHaveAccessToGame(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $url = "/game/myrmes/" . $gameId;
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testPlayersHaveAccessToGameWhenWorkshopPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $url = "/game/myrmes/" . $gameId;
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testPlayersHaveAccessToGameWhenGameIsPaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId;
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testPlayersHaveAccessToGameWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId;
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testShowPersonalBoard(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $url = "/game/myrmes/" . $gameId . "/show/personalBoard";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testShowPersonalBoardWhenMustDropResource(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $url = "/game/myrmes/" . $gameId . "/show/personalBoard";
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $resource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $resource->setQuantity(6);
        $this->entityManager->persist($resource);
        $player->setPhase(MyrmesParameters::PHASE_WINTER);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testShowPersonalBoardWhenGameIsPaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/show/personalBoard";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testShowPersonalBoardWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/show/personalBoard";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayPersonalBoard(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $url = "/game/myrmes/" . $gameId . "/displayPersonalBoard/" . $player->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testDisplayPersonalBoardWhenGameIsPaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/displayPersonalBoard/" . $player->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayPersonalBoardWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/displayPersonalBoard/" . $player->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardActionsWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/mainBoard/box/" . $tile->getId() . "/actions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardActionsWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/mainBoard/box/" . $tile->getId() . "/actions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    private function initializeGameWithTwoPlayers() : int
    {
        $this->client = static::createClient();
        $this->MYRGameManagerService = static::getContainer()->get(MYRGameManagerService::class);
        $this->gameUserRepository = static::getContainer()->get(GameUserRepository::class);
        $this->playerMYRRepository = static::getContainer()->get(PlayerMYRRepository::class);
        $this->gameMYRRepository = static::getContainer()->get(GameMYRRepository::class);
        $this->resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->MYRService = static::getContainer()->get(MYRService::class);
        $user1 = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user1);
        $gameId = $this->MYRGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneById($gameId);
        $this->MYRGameManagerService->createPlayer("test0", $game);
        $this->MYRGameManagerService->createPlayer("test1", $game);

        try {
            $this->MYRGameManagerService->launchGame($game);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return $gameId;
    }
}