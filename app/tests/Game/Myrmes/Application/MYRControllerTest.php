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
        $game->setPhasel;;;;;;;;;;;;;;;;;;;;;;;;;;(MyrmesParameters::PHASE_WORKSHOP);
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