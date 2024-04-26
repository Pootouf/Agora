<?php

namespace App\Tests\Game\Myrmes\Application;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\GameUserRepository;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\AnthillWorkerMYRRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
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
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/0/0";
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
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/0/0";
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
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/0/0";
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
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/0/0";
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
        $url = "/game/myrmes/" . $gameId . "/moveAnt/neededResources/soldierNb/10/5";
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