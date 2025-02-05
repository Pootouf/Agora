<?php

namespace App\Tests\Service\Platform;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\User;
use App\Service\Game\GameManagerService;
use App\Service\Platform\BoardManagerService;
use App\Service\Platform\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\HubInterface;

class BoardManagerServiceTest extends TestCase
{
    private GameManagerService $gameManagerServiceMock;
    private EntityManagerInterface $entityManagerInterfaceMock;
    private NotificationService $notificationService;

    protected function setUp(): void
    {
        $this->gameManagerServiceMock = $this->createMock(GameManagerService::class);
        $this->entityManagerInterfaceMock = $this->createMock(EntityManagerInterface::class);
        $this->notificationService = new NotificationService(
            $this->createMock(HubInterface::class), $this->entityManagerInterfaceMock
        );
    }

    public function testSetUpBoard(): void
    {

        // GIVEN
        $boardManagerService = new BoardManagerService(
            $this->gameManagerServiceMock, $this->entityManagerInterfaceMock, $this->notificationService
        );
        $board = new Board();
        $game = new Game();
        $game->setLabel("6QP");

        // WHEN
        $result = $boardManagerService->setUpBoard($board, $game);

        // THEN
        $this->assertSame(1, $result);
        $this->assertInstanceOf(\DateTime::class, $board->getCreationDate());
        $this->assertInstanceOf(\DateTime::class, $board->getInvitationTimer());
        $this->assertInstanceOf(\DateTime::class, $board->getInactivityTimer());
        $this->assertNotNull($board->getPartyId());
    }

    public function testAddUserToBoard(): void
    {
        // GIVEN
        $boardManagerService = new BoardManagerService(
            $this->gameManagerServiceMock, $this->entityManagerInterfaceMock, $this->notificationService
        );
        $board = new Board();
        $game = new Game();
        $game->setLabel("6QP");
        $result = $boardManagerService->setUpBoard($board, $game);
        $user = new User();
        $board->setNbUserMax(2);

        // WHEN
        $result = $boardManagerService->addUserToBoard($board,  $user);

        // THEN
        $this->assertSame(1, $result);
        $this->assertCount(1, $board->getListUsers());
    }

    public function testRemovePlayerFromBoard(): void
    {
        // GIVEN
        $boardManagerService = new BoardManagerService(
            $this->gameManagerServiceMock, $this->entityManagerInterfaceMock, $this->notificationService
        );
        $board = new Board();
        $game = new Game();
        $game->setLabel("6QP");
        $result = $boardManagerService->setUpBoard($board, $game);
        $user = new User();
        $boardManagerService->addUserToBoard($board,  $user);

        // WHEN
        $result = $boardManagerService->removePlayerFromBoard($board, $user);

        // THEN
        $this->assertSame(1, $result);
        $this->assertCount(0, $board->getListUsers());
    }
}