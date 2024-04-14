<?php

namespace App\Tests\Entity\Platform;

use App\Entity\Platform\Notification;
use App\Entity\Platform\User;
use PHPUnit\Framework\TestCase;

class NotificationEntityUnitTest extends TestCase
{


    public function testGetAndSetReceiver(): void
    {
        $notification = new Notification();
        $user = new User();
        $notification->setReceiver($user);
        $this->assertSame($user, $notification->getReceiver());
    }

    public function testGetAndSetContent(): void
    {
        $notification = new Notification();
        $notification->setContent('Test content');
        $this->assertSame('Test content', $notification->getContent());
    }

    public function testGetAndSetCreatedAt(): void
    {
        $notification = new Notification();
        $createdAt = new \DateTime();
        $notification->setCreatedAt($createdAt);
        $this->assertSame($createdAt, $notification->getCreatedAt());
    }

    public function testIsAndSetIsRead(): void
    {
        $notification = new Notification();
        $notification->setIsRead(true);
        $this->assertTrue($notification->isIsRead());
    }

    public function testConstructorSetsCreatedAt(): void
    {
        $notification = new Notification();
        $this->assertInstanceOf(\DateTime::class, $notification->getCreatedAt());
    }
}