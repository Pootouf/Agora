<?php

namespace App\Tests\Service\Platform\Integration;

use App\Entity\Platform\Game;
use App\Entity\Platform\Board;
use App\Entity\Platform\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Service\Platform\UserService;
use App\Repository\Platform\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;

class UserServiceIntegrationTest extends KernelTestCase
{
    private $entityManager;
    private $userService;
    private static int $ADD_HIMSELF_ERROR = -1;
    private static int $ALREADY_CONTACT_ERROR = -2;
    private static int $NOT_A_CONTACT_ERROR = -3;
    private static int $SUCCESS = 0;

    protected function setUp(): void
    {
        self::bootKernel(); // Initialiser le noyau Symfony
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class); 
        $this->userService = static::getContainer()->get(UserService::class); 
    }

    public function testAddContactWhenSuccess(): void
    {
        // GET
        $user1 = new User();
        $user1->setEmail('test1@example.com');
        $user1->setPassword('password123');
        $user1->setUsername('testuser1');
        $user2 = new User();
        $user2->setEmail('test2@example.com');
        $user2->setPassword('password123');
        $user2->setUsername('testuser2');
        $this->entityManager->persist($user1);
        $this->entityManager->persist($user2);
        $this->entityManager->flush();

        // WHEN
        $result = $this->userService->addContact($user1->getId(), $user2->getId());


        //THEN
        $this->assertSame(UserServiceIntegrationTest::$SUCCESS, $result);
        $this->assertTrue($user1->getContacts()->contains($user2));
    }

    public function testAddContactWhenHimself(): void
    {
        // GET
        $user1 = new User();
        $user1->setEmail('test1@example.com');
        $user1->setPassword('password123');
        $user1->setUsername('testuser1');
        $this->entityManager->persist($user1);
        $this->entityManager->flush();

        // WHEN
        $result = $this->userService->addContact($user1->getId(), $user1->getId());


        //THEN
        $this->assertSame(UserServiceIntegrationTest::$ADD_HIMSELF_ERROR, $result);
        $this->assertFalse($user1->getContacts()->contains($user1));
    }

    public function testAddContactWhenAlready(): void
    {
        // GET
        $user1 = new User();
        $user1->setEmail('test1@example.com');
        $user1->setPassword('password123');
        $user1->setUsername('testuser1');
        $user2 = new User();
        $user2->setEmail('test2@example.com');
        $user2->setPassword('password123');
        $user2->setUsername('testuser2');
        $this->entityManager->persist($user1);
        $this->entityManager->persist($user2);
        $this->entityManager->flush();
        $this->userService->addContact($user1->getId(), $user2->getId());

        // WHEN
        $result = $this->userService->addContact($user1->getId(), $user2->getId());


        //THEN
        $this->assertSame(UserServiceIntegrationTest::$ALREADY_CONTACT_ERROR, $result);
    }

    public function testRemoveContactWhenSuccess(): void
    {
        // GET
        $user1 = new User();
        $user1->setEmail('test1@example.com');
        $user1->setPassword('password123');
        $user1->setUsername('testuser1');
        $user2 = new User();
        $user2->setEmail('test2@example.com');
        $user2->setPassword('password123');
        $user2->setUsername('testuser2');
        $this->entityManager->persist($user1);
        $this->entityManager->persist($user2);
        $this->entityManager->flush();
        $result = $this->userService->addContact($user1->getId(), $user2->getId());
        $this->entityManager->flush();

        // WHEN
        $result = $this->userService->removeContact($user1->getId(), $user2->getId());


        //THEN
        $this->assertSame(UserServiceIntegrationTest::$SUCCESS, $result);
        $this->assertFalse($user1->getContacts()->contains($user2));
    }

    public function testRemoveContactWhenNotAContact(): void
    {
        // GET
        $user1 = new User();
        $user1->setEmail('test1@example.com');
        $user1->setPassword('password123');
        $user1->setUsername('testuser1');
        $user2 = new User();
        $user2->setEmail('test2@example.com');
        $user2->setPassword('password123');
        $user2->setUsername('testuser2');
        $this->entityManager->persist($user1);
        $this->entityManager->persist($user2);
        $this->entityManager->flush();

        // WHEN
        $result = $this->userService->removeContact($user1->getId(), $user2->getId());


        //THEN
        $this->assertSame(UserServiceIntegrationTest::$NOT_A_CONTACT_ERROR, $result);
    }

}