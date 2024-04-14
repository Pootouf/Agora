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
use App\Entity\Platform\Notification;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationServiceUnitTest extends TestCase
{

    private $hubMock;
    private $entityManagerMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer des mocks pour le HubInterface et l'EntityManagerInterface
        $this->hubMock = $this->getMockBuilder(HubInterface::class)->getMock();
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
    }

    public function testNotifyGroup()
    {
        // GIVEN
        $notificationService = new NotificationService($this->hubMock, $this->entityManagerMock);

        // // THEN
        $this->hubMock->expects($this->once())
                      ->method('publish');

        // WHEN
        $notificationService->notifyGroup('test/topic', 'Test content', new \DateTime());
    }

    public function testNotifyUser()
    {
        // GIVEN
        $notificationService = new NotificationService($this->hubMock, $this->entityManagerMock);

        // // THEN
        $this->hubMock->expects($this->once())
                      ->method('publish');

        // WHEN
        $notificationService->notifyUser('testUser', 'Test content', new \DateTime());
    }

    public function testStoreNotification()
    {
        // GIVEN
        $notificationService = new NotificationService($this->hubMock, $this->entityManagerMock);
        $userMock = $this->getMockBuilder(User::class)
                    ->disableOriginalConstructor()
                    ->getMock();

        // THEN
        $this->entityManagerMock->expects($this->once())
                                 ->method('persist')
                                 ->with($this->isInstanceOf(Notification::class));

        // WHEN
        $notificationService->storeNotification($userMock, 'Test content');
    }


    public function testNotifyManyUser()
{
    // GIVEN
    $notificationService = new NotificationService($this->hubMock, $this->entityManagerMock);
    $user1 = $this->getMockBuilder(User::class)->getMock();
    $user2 = $this->getMockBuilder(User::class)->getMock();
    $user3 = $this->getMockBuilder(User::class)->getMock();
    $users = [$user1, $user2, $user3];

    // THEN
    $this->entityManagerMock->expects($this->exactly(3))
                             ->method('persist')
                             ->with($this->isInstanceOf(Notification::class));
    $this->hubMock->expects($this->exactly(3))
                  ->method('publish');

    // WHEN
    $notificationService->notifyManyUser($users, 'Test content', new \DateTime());

}
}