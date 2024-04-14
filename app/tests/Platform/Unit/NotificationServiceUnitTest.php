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
        private $notificationService;
    
        protected function setUp(): void
        {
            $this->hubMock = $this->createMock(HubInterface::class);
            $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
            $this->notificationService = new NotificationService($this->hubMock, $this->entityManagerMock);
        }
    
public function testNotifyGroup(): void
{
    $topic = 'test_topic';
    $content = 'Test content';
    $date = new \DateTime();

    $this->hubMock->expects($this->once())
        ->method('publish')
        ->with($this->callback(function ($update) use ($topic, $content, $date) {
            // Check if the topics match
            if ($update->getTopics() !== [$topic]) {
                return false;
            }

            // Decode the data from JSON
            $data = json_decode($update->getData(), true);

            // Check if the content and date match
            return $data['content'] === $content && $data['date'] === $date->format('Y-m-d H:i:s.u');
        }));

    $this->notificationService->notifyGroup($topic, $content, $date);
}
    
        public function testNotifyUser(): void
        {
            $user = 1;
            $content = 'Test content';
            $date = new \DateTime();
            $expectedUpdate = new Update('agora/notifications/user/' . $user, json_encode(['content' => $content, 'date' => $date->format('Y-m-d H:i:s.u')]));
    
            $this->hubMock->expects($this->once())
                ->method('publish')
                ->with($expectedUpdate);
    
            $this->notificationService->notifyUser($user, $content, $date);
        }
    
        public function testStoreNotification(): void
        {
            $user = 'test_user';
            $content = 'Test content';
    
            $this->entityManagerMock->expects($this->once())
                ->method('persist')
                ->with($this->isInstanceOf(Notification::class));
    
            $this->notificationService->storeNotification($user, $content);
        }
    
        public function testNotifyManyUser(): void
        {
            $users = [1, 2, 3];
            $content = 'Test content';
            $date = new \DateTime();
    
            $this->entityManagerMock->expects($this->exactly(count($users)))
                ->method('persist');
    
            $this->entityManagerMock->expects($this->once())
                ->method('flush');
    
            foreach ($users as $userId) {
                $this->hubMock->expects($this->atLeastOnce())
                    ->method('publish')
                    ->with(new Update('agora/notifications/user/' . $userId, json_encode(['content' => $content, 'date' => $date->format('Y-m-d H:i:s.u')])));
            }
    
            $this->notificationService->notifyManyUser($users, $content, $date);
        }
}
 