<?php

namespace App\Tests;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\User;
use PHPUnit\Framework\TestCase;

class BoardEntityTest extends TestCase
{

    public function testNbUserMax()
    {
        // GIVEN
        $board = new Board();

        // WHEN
        $board->setNbUserMax(5);

        // THEN
        $this->assertEquals(5, $board->getNbUserMax());
    }

    public function testAddUserToListUsers()
    {
        // GIVEN
        $board = new Board();
        $user = new User();
        $board->setNbUserMax(5);

        // WHEN
        $board->addListUser($user);

        // THEN
        $this->assertTrue($board->getListUsers()->contains($user));
    }


    public function testRemoveUserFromListUsers()
    {
        // GIVEN
        $board = new Board();
        $user = new User();

        // WHEN
        $board->addListUser($user);
        $board->removeListUser($user);

        // THEN
        $this->assertFalse($board->getListUsers()->contains($user));
    }


    public function testIsAvailable()
    {
        // GIVEN
        $board = new Board();
        $board->setNbUserMax(4);
        $user1 = new User();
        $user2 = new User();
        $user3 = new User();

        // WHEN
        $board->addListUser($user1);
        $board->addListUser($user2);
        $board->addListUser($user3);
 
        // THEN
        $this->assertTrue($board->isAvailble());

    }


    public function testIsNotAvailable()
    {
        // GIVEN
        $board = new Board();
        $board->setNbUserMax(4);
        $user1 = new User();
        $user2 = new User();
        $user3 = new User();
        $user4 = new User();

        // WHEN
        $board->addListUser($user1);
        $board->addListUser($user2);
        $board->addListUser($user3);
        $board->addListUser($user4);

        // THEN
        $this->assertFalse($board->isAvailble());

    }



    public function testCreationDate()
    {

        // GIVEN
        $board = new Board();
        $date = new \DateTime('2024-03-01');

        // WHEN
        $board->setCreationDate($date);
        $this->assertEquals($date, $board->getCreationDate());
    }

    public function testInvitationTimer()
    {
        // GIVEN
        $board = new Board();
        $time = new \DateTime('12:00:00');

        // WHEN
        $board->setInvitationTimer($time);

        // THEN
        $this->assertEquals($time, $board->getInvitationTimer());
    }

    public function testInvitationHash()
    {
        // GIVEN
        $board = new Board();
        $invitationHash = 'abcdef123456';

        // WHEN
        $board->setInvitationHash($invitationHash);

        // THEN
        $this->assertEquals($invitationHash, $board->getInvitationHash());
    }

    public function testNbInvitations()
    {
        // GIVEN
        $board = new Board();
        $invitations = 5;

        // WHEN
        $board->setNbInvitations($invitations);

        // THEN
        $this->assertEquals($invitations, $board->getNbInvitations());
    }

    public function testUsersNb()
    {
        // GIVEN
        $board = new Board();
        $user1 = new User();

        // WHEN
        $board->setNbUserMax(5);
        $board->addListUser($user1);

        // THEN
        $this->assertEquals(1, $board->getUsersNb());

    }

    public function testGetSetInactivityHours()
    {
        // GIVEN
        $board = new Board();
        $hours = 24;

        // WHEN
        $board->setInactivityHours($hours);

        // THEN
        $this->assertEquals($hours, $board->getInactivityHours());
    }

    public function testHasUser()
    {
        // GIVEN
        $board = new Board();
        $user = new User();
        $board->setNbUserMax(5);

        // WHEN
        $board->addListUser($user);

        // THEN
        $this->assertTrue($board->hasUser($user));

    }


    public function testSetGame()
    {
        // GIVEN
        $board = new Board();
        $game = new Game();

        // WHEN
        $board->setGame($game);

        // THEN
        $this->assertEquals($game, $board->getGame());
    }

    public function testPartyId()
    {
        // GIVEN
        $board = new Board();
        $partyId = 123;

        // WHEN
        $board->setPartyId($partyId);

        // THEN
        $this->assertEquals($partyId, $board->getPartyId());
    }

    public function testIsFull()
    {
        // GIVEN
        $board = new Board();
        $board->setNbUserMax(2);
        $user1 = new User();
        $user2 = new User();

        // WHEN
        $board->addListUser($user1);
        $board->addListUser($user2);

        // THEN
        $this->assertTrue($board->isFull());
    }
}