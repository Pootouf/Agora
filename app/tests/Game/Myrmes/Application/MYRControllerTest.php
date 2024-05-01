<?php

namespace App\Tests\Game\Myrmes\Application;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\GameUserRepository;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\AnthillWorkerMYRRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
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

    private PlayerResourceMYRRepository $playerResourceMYRRepository;
    private EntityManagerInterface $entityManager;
    private GameMYRRepository $gameMYRRepository;
    private MYRService $MYRService;

    private AnthillHoleMYRRepository $anthillHoleMYRRepository;

    private TileTypeMYRRepository $tileTypeMYRRepository;

    private TileMYRRepository $tileMYRRepository;
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
        $game->setLaunched(false);
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
        $game->setLaunched(false);
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
        $player = $game->getPlayers()->last();
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
        $game->setLaunched(false);
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
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/display/mainBoard/box/" . $tile->getId() . "/actions";
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
        $url = "/game/myrmes/" . $gameId . "/display/mainBoard/box/" . $tile->getId() . "/actions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardActionsWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $url = "/game/myrmes/" . $gameId . "/display/mainBoard/box/" . $tile->getId() . "/actions";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardActions(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $url = "/game/myrmes/" . $gameId . "/display/mainBoard/box/" . $tile->getId() . "/actions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testDisplayMainBoardActionsWorkerPhaseWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/workerPhase/displayBoardBoxActions/10/5/0/" . $tile->getId() . "/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardActionsWorkerPhaseWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/workerPhase/displayBoardBoxActions/10/5/0/" . $tile->getId() . "/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardActionsWorkerPhaseWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $url = "/game/myrmes/" . $gameId . "/workerPhase/displayBoardBoxActions/10/5/0/" . $tile->getId() . "/ ";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardActionsWorkerPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $url = "/game/myrmes/" . $gameId . "/workerPhase/displayBoardBoxActions/10/5/0/" . $tile->getId() . "/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testDisplayObjectivesWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/display/objectives";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayObjectivesWhenGameIsPaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/display/objectives";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayObjectives(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $tile = $game->getMainBoardMYR()->getTiles()->first();
        $url = "/game/myrmes/" . $gameId . "/display/objectives";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testDisplayThrowResourceActionsWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $playerResource = $game->getPlayers()->first()->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/display/personalBoard/throwResource/" . $playerResource->getId() . "/actions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayThrowResourceActionsWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $playerResource = $game->getPlayers()->first()->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/display/personalBoard/throwResource/" . $playerResource->getId() . "/actions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayThrowResourceActionsWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $playerResource = $game->getPlayers()->first()->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $url = "/game/myrmes/" . $gameId . "/display/personalBoard/throwResource/" . $playerResource->getId() . "/actions";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayThrowResourceActions(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $playerResource = $game->getPlayers()->first()->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $url = "/game/myrmes/" . $gameId . "/display/personalBoard/throwResource/" . $playerResource->getId() . "/actions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testUpBonusWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/up/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testUpBonusWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/up/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testUpBonusWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/up/bonus/";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testUpBonusWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $url = "/game/myrmes/" . $gameId . "/up/bonus/";
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testUpBonusWhenCannot(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->getPlayers()->first()->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $url = "/game/myrmes/" . $gameId . "/up/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testUpBonus(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/up/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }


    public function testLowerBonusWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/lower/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testlowerBonusWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/lower/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testlowerBonusWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/lower/bonus/";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testlowerBonusWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $url = "/game/myrmes/" . $gameId . "/lower/bonus/";
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testlowerBonusWhenCannot(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->getPlayers()->first()->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $url = "/game/myrmes/" . $gameId . "/lower/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testlowerBonus(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/lower/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testConfirmBonusWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/confirm/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmBonusWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/confirm/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmBonusWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/confirm/bonus/";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmBonusWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $url = "/game/myrmes/" . $gameId . "/confirm/bonus/";
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmBonus(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->getPlayers()->last()->setPhase(MyrmesParameters::PHASE_BIRTH);
        $url = "/game/myrmes/" . $gameId . "/confirm/bonus/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testPlaceNurseWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/placeNurse/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testPlaceNurseWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/placeNurse/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testPlaceNurseWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/placeNurse/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testPlaceNurseWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $url = "/game/myrmes/" . $gameId . "/placeNurse/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testPlaceNurseWhenInvalidArea(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $invalidArea = MyrmesParameters::AREA_COUNT;
        $url = "/game/myrmes/" . $gameId . "/placeNurse/" . $invalidArea;
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testPlaceNurse(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $area = MyrmesParameters::LARVAE_AREA;
        $url = "/game/myrmes/" . $gameId . "/placeNurse/" . $area;
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testConfirmNursesPlacementWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/confirm/nursesPlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmNursesPlacementWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/confirm/nursesPlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmNursesPlacementWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/confirm/nursesPlacement";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmNursesPlacementWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $url = "/game/myrmes/" . $gameId . "/confirm/nursesPlacement";
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmNursesPlacement(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setPhase(MyrmesParameters::PHASE_WORKER);
        $area = MyrmesParameters::LARVAE_AREA;
        $url = "/game/myrmes/" . $gameId . "/placeNurse/" . $area;
        $this->client->request("GET", $url);
        $url = "/game/myrmes/" . $gameId . "/confirm/nursesPlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testConfirmNursesPlacementWhenGameNotWorkerPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $area = MyrmesParameters::LARVAE_AREA;
        $url = "/game/myrmes/" . $gameId . "/placeNurse/" . $area;
        $this->client->request("GET", $url);
        $url = "/game/myrmes/" . $gameId . "/confirm/nursesPlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testCancelNursesPlacementWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/cancel/nursesPlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCancelNursesPlacementWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/cancel/nursesPlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCancelNursesPlacementWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/cancel/nursesPlacement";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCancelNursesPlacementWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $url = "/game/myrmes/" . $gameId . "/cancel/nursesPlacement";
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCancelNursesPlacement(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $url = "/game/myrmes/" . $gameId . "/cancel/nursesPlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testSelectAnthillHoleToSendWorkerWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/games/myrmes/" . $gameId . "/selectAntHillHoleToSendWorker";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testSelectAntHillHoleToSendWorkerWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/games/myrmes/" . $gameId . "/selectAntHillHoleToSendWorker";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testSelectAntHillHoleToSendWorkerWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/games/myrmes/" . $gameId . "/selectAntHillHoleToSendWorker";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testSelectAntHillHoleToSendWorker(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $url = "/games/myrmes/" . $gameId . "/selectAntHillHoleToSendWorker";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardDuringWorkerPhaseWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/workerPhase/mainBoard/10/5/0/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardDuringWorkerPhaseWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/workerPhase/mainBoard/10/5/0/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardDuringWorkerPhaseWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workerPhase/mainBoard/10/5/0/ ";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testDisplayMainBoardDuringWorkerPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $url = "/game/myrmes/" . $gameId . "/workerPhase/mainBoard/10/5/0/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testPlaceWorkerOnColonyLevelTrackWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnColonyLevelTrack/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnColonyLevelTrackWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnColonyLevelTrack/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnColonyLevelTrackWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnColonyLevelTrack/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnColonyLevelTrackWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnColonyLevelTrack/1";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnColonyLevelTrackWhenNotPhaseWorker(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnColonyLevelTrack/1";
        $game->setGamePhase(MyrmesParameters::PHASE_EVENT);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnColonyLevelTrackWhenLevelNotExists(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnColonyLevelTrack/5";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnColonyLevelTrack(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnColonyLevelTrack/0";
        $player = $game->getPlayers()->first();
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $workers = $player->getPersonalBoardMYR()->getAnthillWorkers();
        for ($i = 0; $i < $workers->count() - 1; ++$i) {
            $workers->get($i)->setWorkFloor(MyrmesParameters::WORKSHOP_AREA);
            $this->entityManager->persist($workers->get($i));
        }
        $this->entityManager->flush();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testPlaceWorkerOnAnthillHoleWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnAntHillHole/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnAntHillHoleWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnAntHillHole/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnAntHillHoleWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnAntHillHole/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnAntHillHoleWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnAntHillHole/1";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnAntHillHoleWhenNotPhaseWorker(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnAntHillHole/1";
        $game->setGamePhase(MyrmesParameters::PHASE_EVENT);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnAntHillHoleWhenHoleNotOwnedByPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $player = $game->getPlayers()->last();
        $anthillHole = $this->anthillHoleMYRRepository->findOneBy([
            'player' => $player
        ]);
        $tile = $anthillHole->getTile();
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnAntHillHole/" . $tile->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceWorkerOnAntHillHole(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $player = $game->getPlayers()->first();
        $anthillHole = $this->anthillHoleMYRRepository->findOneBy([
            'player' => $player
        ]);
        $tile = $anthillHole->getTile();
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/placeWorkerOnAntHillHole/" . $tile->getId();
        $this->entityManager->flush();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testNeededSoldiersToMoveWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/0/0/[]";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testNeededSoldiersToMoveWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/0/0/[]";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testNeededSoldiersToMoveWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/0/0/[]";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testNeededSoldiersToMoveWhenTileIsNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/0/0/[]";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testNeededSoldiersToMove(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/10/5/[]";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }


    public function testNeededMovementPointsWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/movementPoints/originTile/0/0/destinationTile/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testNeededMovementPointsWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/movementPoints/originTile/0/0/destinationTile/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testNeededMovementPointsWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/movementPoints/originTile/0/0/destinationTile/0/0";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testNeededMovementPointsWhenTileIsNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/movementPoints/originTile/0/0/destinationTile/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testNeededMovementPoints(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/movementPoints" .
            "/originTile/5/10/destinationTile/10/5";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testIsValidTileToMoveAntWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/isValid/tile/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testIsValidTileToMoveAntWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/isValid/tile/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testIsValidTileToMoveAntWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/isValid/tile/0/0";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }
    public function testIsValidTileToMoveAnt(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/isValid/tile/10/5";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testCanCleanPheromoneWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/canClean/pheromone/0/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCanCleanPheromoneWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/canClean/pheromone/0/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCanCleanPheromoneWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/canClean/pheromone/0/0/0";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCanCleanPheromoneWhenTileNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/canClean/pheromone/0/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testCanCleanPheromoneWhenPheromoneIsNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/canClean/pheromone/10/5/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }
    public function testCanCleanPheromone(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/canClean/pheromone/10/5/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testCanPlacePheromoneWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/canPlace/pheromone/0/0/0/0/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCanPlacePheromoneWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/canPlace/pheromone/0/0/0/0/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCanPlacePheromoneWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/canPlace/pheromone/0/0/0/0/ ";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCanPlacePheromoneWhenTileNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/canPlace/pheromone/0/0/1/0/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testCanCleanPheromoneWhenTypeIsNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/canPlace/pheromone/0/0/12/0/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }
    public function testCanPlacePheromone(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $url = "/game/myrmes/" . $gameId . "/canPlace/pheromone/10/5/1/0/ ";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testCleanPheromoneWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCleanPheromoneWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCleanPheromoneWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/0/0";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCleanPheromoneWhenNotActive(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/0/0";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCleanPheromoneWhenNotWorkerPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/0/0";
        $user2 = $this->gameUserRepository->findOneByUsername("test0");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCleanPheromoneWhenTileNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testCleanPheromoneWhenPheromoneNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/5/10";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testCleanPheromoneWhenCannotCleanBecauseNoDirt(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/10/5";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testCleanPheromone(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $player = $game->getPlayers()->first();
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        $playerDirt = $this->playerResourceMYRRepository->findOneBy(
            ["resource" => $dirt, "personalBoard" => $player->getPersonalBoardMYR()]
        );
        $playerDirt->setQuantity(1);
        $this->entityManager->persist($playerDirt);
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $this->entityManager->persist($game);
        $workers = $player->getPersonalBoardMYR()->getAnthillWorkers();
        for ($i = 0; $i < $workers->count(); ++$i) {
            $workers->get($i)->setWorkFloor(MyrmesParameters::WORKSHOP_AREA);
            $this->entityManager->persist($workers->get($i));
        }
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/10/5";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testGetPheromoneTilesCoordsWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/getPheromoneTiles/coords/givenTile/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testGetPheromoneTilesCoordsWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/clean/pheromone/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testGetPheromoneTilesCoordsWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/getPheromoneTiles/coords/givenTile/0/0";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testGetPheromoneTilesCoordsWhenTileNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/getPheromoneTiles/coords/givenTile/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testGetPheromoneTilesCoordsWhenPheromoneIsNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/getPheromoneTiles/coords/givenTile/10/5";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testGetPheromoneTilesCoords(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/getPheromoneTiles/coords/givenTile/10/5";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }


    public function testGetTileIdFromCoordsWhenTileIsNull(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/getTile/id/coords/0/0";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR,
            $this->client->getResponse());
    }

    public function testTilesIdFromCoords(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/getTile/id/coords/10/5";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testMoveAntWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/direction/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testMoveAntWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/direction/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testMoveAntWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/direction/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testMoveAntWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->last();
        $player->setTurnOfPlayer(false);
        $url = "/game/myrmes/" . $gameId . "/moveAnt/direction/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testMoveAntWhenGameNotWorkerPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/direction/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testMoveAntWhenNoAnt(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $player->getGardenWorkerMYRs()->clear();
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/direction/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testMoveAntWhenPlayerCantMoveAnt(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 6, "coordY" => 5]);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($player);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $player->addGardenWorkerMYR($gardenWorker);
        $this->entityManager->persist($player);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/direction/3";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testMoveAnt(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $ant = $player->getGardenWorkerMYRs()->first();
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 6, "coordY" => 3]);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($player);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $this->entityManager->persist($gardenWorker);
        $player->addGardenWorkerMYR($gardenWorker);
        $this->entityManager->persist($player);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/moveAnt/direction/3";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testConfirmActionWorkerPhaseWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/confirm/action/workerPhase/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmActionWorkerPhaseWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/confirm/action/workerPhase/";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmActionWorkerPhaseWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/confirm/action/workerPhase/";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmActionWorkerPhaseWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/confirm/action/workerPhase/";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmActionWorkerPhaseWhenNotPhaseWorker(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/confirm/action/workerPhase/";
        $game->setGamePhase(MyrmesParameters::PHASE_EVENT);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testConfirmActionWorkerPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/confirm/action/workerPhase/";
        $player = $game->getPlayers()->first();
        $game->setGamePhase(MyrmesParameters::PHASE_WORKER);
        $this->entityManager->persist($game);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 6, "coordY" => 3]);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($player);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $this->entityManager->persist($gardenWorker);
        $player->addGardenWorkerMYR($gardenWorker);
        $this->entityManager->persist($player);
        $workers = $player->getPersonalBoardMYR()->getAnthillWorkers();
        for ($i = 0; $i < $workers->count(); ++$i) {
            $workers->get($i)->setWorkFloor(MyrmesParameters::WORKSHOP_AREA);
            $this->entityManager->persist($workers->get($i));
        }
        $this->entityManager->flush();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testHarvesResourceWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/harvestResource/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testHarvestResourceWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/harvestResource/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testHarvestResourceWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/harvestResource/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testHarvestResourceWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/harvestResource/1";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testHarvestResourceWhenNotCannot(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(true);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $resource = $this->resourceMYRRepository->findOneBy(["id" => 1]);
        $pheromoneTile->setResource($resource);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $player->addPheromonMYR($pheromone);
        $player->setRemainingHarvestingBonus(0);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/harvestResource/" . $tile->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testHarvestResource(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $resource = $this->resourceMYRRepository->findOneBy(["id" => 1]);
        $pheromoneTile->setResource($resource);
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $player->addPheromonMYR($pheromone);
        $player->setRemainingHarvestingBonus(1);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/harvestResource/" . $tile->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testEndHarvestPhaseWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/end/harvestPhase";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testEndHarvestPhaseWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/end/harvestPhase";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testEndHarvestPhaseWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/end/harvestPhase";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testEndHarvestPhaseWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/end/harvestPhase";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testEndHarvestPhaseWhenNotAllHarvested(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(false);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $resource = $this->resourceMYRRepository->findOneBy(["id" => 1]);
        $pheromoneTile->setResource($resource);
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $player->addPheromonMYR($pheromone);
        $player->setRemainingHarvestingBonus(1);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/end/harvestPhase";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testEndHarvestPhaseWhenCanDoWorkshopAction(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(true);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $resource = $this->resourceMYRRepository->findOneBy(["id" => 1]);
        $pheromoneTile->setResource($resource);
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $player->addPheromonMYR($pheromone);
        $player->setRemainingHarvestingBonus(1);
        $player->getPersonalBoardMYR()->getNurses()->first()->setArea(MyrmesParameters::WORKSHOP_AREA);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/end/harvestPhase";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testEndHarvestPhaseWhenCanSetWinter(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $seasons = $game->getMainBoardMYR()->getSeasons();
        foreach ($seasons as $season) {
            if ($season->getName() === MyrmesParameters::FALL_SEASON_NAME) {
                $season->setActualSeason(true);
            } else {
                $season->setActualSeason(false);
            }
            $this->entityManager->persist($season);
        }
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(true);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $resource = $this->resourceMYRRepository->findOneBy(["id" => 1]);
        $pheromoneTile->setResource($resource);
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $player->addPheromonMYR($pheromone);
        $player->setRemainingHarvestingBonus(1);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/end/harvestPhase";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }


    public function testEndHarvestPhaseWhenNewSeason(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $tileType = $this->tileTypeMYRRepository->findOneBy(["id" => 1]);
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 10, "coordY" => 5]);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($player);
        $pheromone->setHarvested(true);
        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($tile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setResource(null);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $resource = $this->resourceMYRRepository->findOneBy(["id" => 1]);
        $pheromoneTile->setResource($resource);
        $this->entityManager->persist($pheromoneTile);
        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);
        $player->addPheromonMYR($pheromone);
        $player->setRemainingHarvestingBonus(1);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/end/harvestPhase";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testactivateAnthillHolePlacementWorkshopWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testactivateAnthillHolePlacementWorkshopWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testactivateAnthillHolePlacementWorkshopWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testactivateAnthillHolePlacementWorkshopWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testactivateAnthillHolePlacementWorkshop(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testplaceAnthillHoleWorkshopWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceAnthillHoleWorkshopWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceAnthillHoleWorkshopWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceAnthillHoleWorkshopWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement/1";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceAnthillHoleWorkshopWhenCannot(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testplaceAnthillHoleWorkshop(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($game);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($player);
        $player->getPersonalBoardMYR()->getNurses()->first()->setArea(MyrmesParameters::WORKSHOP_AREA);
        $this->entityManager->persist($player->getPersonalBoardMYR()->getNurses()->first());
        $this->entityManager->flush();
        $anthillHole = $player->getAnthillHoleMYRs()->first();
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => $anthillHole->getTile()->getCoordX(),
            "coordY" => $anthillHole->getTile()->getCoordY() + 2]);
        $url = "/game/myrmes/" . $gameId . "/workshop/activate/anthillHolePlacement/" . $tile->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testIncreaseAnthillLevelWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/workshop/increaseAnthillLevel";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testincreaseAnthillLevelWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/workshop/increaseAnthillLevel";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testincreaseAnthillLevelWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/increaseAnthillLevel";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testincreaseAnthillLevelWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/increaseAnthillLevel";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testincreaseAnthillLevelWhenCannot(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/increaseAnthillLevel";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testincreaseAnthillLevel(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($game);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $playerGrass = $this->playerResourceMYRRepository->findOneBy(["resource" => $grass,
            "personalBoard" => $player->getPersonalBoardMYR()]);
        $playerGrass->setQuantity(2);
        $this->entityManager->persist($playerGrass);
        $this->entityManager->persist($player);
        $player->getPersonalBoardMYR()->getNurses()->first()->setArea(MyrmesParameters::WORKSHOP_AREA);
        $this->entityManager->persist($player->getPersonalBoardMYR()->getNurses()->first());
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/workshop/increaseAnthillLevel";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testCreateNurseWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/workshop/createNurse";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testcreateNurseWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/workshop/createNurse";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testcreateNurseWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/createNurse";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testcreateNurseWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/createNurse";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testcreateNurseLevelWhenCannot(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/createNurse";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testcreateNurse(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($game);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($player);
        $player->getPersonalBoardMYR()->getNurses()->first()->setArea(MyrmesParameters::WORKSHOP_AREA);
        $this->entityManager->persist($player->getPersonalBoardMYR()->getNurses()->first());
        $foodResource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->filter(
            function (PlayerResourceMYR $playerResource) {
                return $playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS;
            }
        )->first();
        $foodResource->setQuantity(2);
        $this->entityManager->persist($foodResource);
        $player->getPersonalBoardMYR()->setLarvaCount(2);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/workshop/createNurse";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testConfirmWorkshopActionsWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/workshop/confirmWorkshopActions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testconfirmWorkshopActionsWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/workshop/confirmWorkshopActions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testconfirmWorkshopActionsWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/confirmWorkshopActions";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testconfirmWorkshopActionsWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/confirmWorkshopActions";
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testconfirmWorkshopActionsWhenNotWorkshopPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/workshop/confirmWorkshopActions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testconfirmWorkshopActionsWhenCanDoAgainWorkshop(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $game->getPlayers()->first()->getPersonalBoardMYR()->getNurses()->first()->setArea(MyrmesParameters::WORKSHOP_AREA);
        $this->entityManager->persist($game->getPlayers()->first()->getPersonalBoardMYR()->getNurses()->first());
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/workshop/confirmWorkshopActions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testconfirmWorkshopActionsWhenSpring(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/workshop/confirmWorkshopActions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }


    public function testconfirmWorkshopActionsLevelWhenFall(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $seasons = $game->getMainBoardMYR()->getSeasons();
        foreach ($seasons as $season) {
            if ($season->getName() === MyrmesParameters::FALL_SEASON_NAME) {
                $season->setActualSeason(true);
            } else {
                $season->setActualSeason(false);
            }
            $this->entityManager->persist($season);
        }
        $game->setGamePhase(MyrmesParameters::PHASE_WORKSHOP);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/workshop/confirmWorkshopActions";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testThrowResourceWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $player = $game->getPlayers()->first();
        $resource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $url = "/game/myrmes/" . $gameId . "/throwResource/warehouse/" . $resource->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testThrowResourceWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $player = $game->getPlayers()->first();
        $resource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $url = "/game/myrmes/" . $gameId . "/throwResource/warehouse/" . $resource->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testThrowResourceWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $resource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $url = "/game/myrmes/" . $gameId . "/throwResource/warehouse/" . $resource->getId();
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testThrowResourceWhenNotTurnOfPlayer(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $resource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $url = "/game/myrmes/" . $gameId . "/throwResource/warehouse/" . $resource->getId();
        $game->getPlayers()->last()->setTurnOfPlayer(false);
        $user2 = $this->gameUserRepository->findOneByUsername("test1");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testThrowResourceWhenNotWinterPhase(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $player = $game->getPlayers()->first();
        $resource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $url = "/game/myrmes/" . $gameId . "/throwResource/warehouse/" . $resource->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testThrowResourceWhenNotEnoughResource(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WINTER);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $player = $game->getPlayers()->first();
        $resource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $url = "/game/myrmes/" . $gameId . "/throwResource/warehouse/" . $resource->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testThrowResource(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setGamePhase(MyrmesParameters::PHASE_WINTER);
        $this->entityManager->persist($game);
        $player = $game->getPlayers()->first();
        $resource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $resource->setQuantity(1);
        $this->entityManager->persist($resource);
        $this->entityManager->flush();
        $url = "/game/myrmes/" . $gameId . "/throwResource/warehouse/" . $resource->getId();
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }

    public function testdisplayPheromoneAndSpecialTileMenuToPlaceWhenGameNotLaunched(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setLaunched(false);
        $url = "/game/myrmes/" . $gameId . "/display/menu/pheromone/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testdisplayPheromoneAndSpecialTileMenuToPlaceWhenGamePaused(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $game->setPaused(true);
        $url = "/game/myrmes/" . $gameId . "/display/menu/pheromone/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testdisplayPheromoneAndSpecialTileMenuToPlaceWhenSpectator(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/display/menu/pheromone/1";
        $user2 = $this->gameUserRepository->findOneByUsername("test2");
        $this->client->loginUser($user2);
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN,
            $this->client->getResponse());
    }

    public function testdisplayPheromoneAndSpecialTileMenuToPlace(): void
    {
        //GIVEN
        $gameId = $this->initializeGameWithTwoPlayers();
        /** @var GameMYR $game */
        $game = $this->gameMYRRepository->findOneBy(["id" => $gameId]);
        $url = "/game/myrmes/" . $gameId . "/display/menu/pheromone/1";
        //WHEN
        $this->client->request("GET", $url);
        // THEN
        $this->assertResponseStatusCodeSame(Response::HTTP_OK,
            $this->client->getResponse());
    }


    private function initializeGameWithTwoPlayers() : int
    {
        $this->client = static::createClient();
        $this->MYRGameManagerService = static::getContainer()->get(MYRGameManagerService::class);
        $this->gameUserRepository = static::getContainer()->get(GameUserRepository::class);
        $this->playerMYRRepository = static::getContainer()->get(PlayerMYRRepository::class);
        $this->gameMYRRepository = static::getContainer()->get(GameMYRRepository::class);
        $this->anthillHoleMYRRepository = static::getContainer()->get(AnthillHoleMYRRepository::class);
        $this->resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->tileTypeMYRRepository = static::getContainer()->get(TileTypeMYRRepository::class);
        $this->tileMYRRepository = static::getContainer()->get(TileMYRRepository::class);
        $this->MYRService = static::getContainer()->get(MYRService::class);
        $this->playerResourceMYRRepository = static::getContainer()->get(PlayerResourceMYRRepository::class);
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