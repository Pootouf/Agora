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
        // Créer une instance de NotificationService en passant les mocks
        $notificationService = new NotificationService($this->hubMock, $this->entityManagerMock);

        // Vérifier si la méthode notifyGroup appelle bien la méthode publish du hub
        $this->hubMock->expects($this->once())
                      ->method('publish');

        // Appeler la méthode notifyGroup avec des données de test
        $notificationService->notifyGroup('test/topic', 'Test content', new \DateTime());
    }

    public function testNotifyUser()
    {
        // Créer une instance de NotificationService en passant les mocks
        $notificationService = new NotificationService($this->hubMock, $this->entityManagerMock);

        // Vérifier si la méthode notifyUser appelle bien la méthode publish du hub
        $this->hubMock->expects($this->once())
                      ->method('publish');

        // Appeler la méthode notifyUser avec des données de test
        $notificationService->notifyUser('testUser', 'Test content', new \DateTime());
    }

    public function testStoreNotification()
    {
        // Créer une instance de NotificationService en passant les mocks
        $notificationService = new NotificationService($this->hubMock, $this->entityManagerMock);

          // Créer un mock pour l'entité User
        $userMock = $this->getMockBuilder(User::class)
                    ->disableOriginalConstructor()
                    ->getMock();

        // Vérifier si la méthode persist est appelée sur l'entityManagerMock
        $this->entityManagerMock->expects($this->once())
                                 ->method('persist')
                                 ->with($this->isInstanceOf(Notification::class));

        // Appeler la méthode storeNotification avec des données de test
        $notificationService->storeNotification($userMock, 'Test content');
    }
}