<?php

namespace App\Tests\Service\Platform;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\User;
use App\Service\Game\GameManagerService;
use App\Service\Platform\BoardManagerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class BoardManagerServiceTest extends TestCase
{
    private $gameManagerServiceMock;
    private $entityManagerInterfaceMock;

    protected function setUp(): void
    {
        $this->gameManagerServiceMock = $this->createMock(GameManagerService::class);
        $this->entityManagerInterfaceMock = $this->createMock(EntityManagerInterface::class);
    }

    public function testSetUpBoard(): void
    {

        // GIVEN
        $boardManagerService = new BoardManagerService($this->gameManagerServiceMock, $this->entityManagerInterfaceMock);
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
        $boardManagerService = new BoardManagerService($this->gameManagerServiceMock, $this->entityManagerInterfaceMock);
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
        $boardManagerService = new BoardManagerService($this->gameManagerServiceMock, $this->entityManagerInterfaceMock);
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