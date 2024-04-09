<?php

namespace App\Tests;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\User;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{


    public function testEmail()
    {
        // GIVEN
        $user = new User();

        // WHEN
        $user->setEmail('test@example.com');

        // THEN
        $this->assertEquals('test@example.com', $user->getEmail());
    }

    public function testRoles()
    {
        // GIVEN
        $user = new User();
        
        // WHEN
        $user->setRoles(['ROLE_ADMIN']);

        // THEN
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

    public function testPassword()
    {
        // GIVEN
        $user = new User();
        
        // WHEN
        $user->setPassword('password');

        // THEN
        $this->assertEquals('password', $user->getPassword());
    }

    public function testUsername()
    {
        // GIVEN
        $user = new User();
        
        // WHEN
        $user->setUsername('testuser');

        // THEN
        $this->assertEquals('testuser', $user->getUsername());
    }

    public function testIsVerified()
    {
        // GIVEN
        $user = new User();
        
        // WHEN
        $user->setIsVerified(true);

        // THEN
        $this->assertTrue($user->isVerified());
    }

    public function testAddBoard()
    {
        // GIVEN
        $user = new User();
        $board = new Board();
        $board->setNbUserMax(4);

        // WHEN
        $user->addBoard($board);

        // THEN
        $this->assertTrue($user->getBoards()->contains($board));
    }

    public function testRemoveBoard()
    {
        // GIVEN
        $user = new User();
        $board = new Board();
        $board->setNbUserMax(4);

        // WHEN
        $user->addBoard($board);
        $user->removeBoard($board);

        // THEN
        $this->assertFalse($user->getBoards()->contains($board));
    }
    public function testAddFavoriteGame()
    {

        // GIVEN
        $user = new User();
        $game = new Game();

        // WHEN
        $user->addFavoriteGame($game);

        // THEN
        $this->assertTrue($user->getFavoriteGames()->contains($game));
    }

    public function testRemoveFavoriteGame()
    {
        // GIVEN
        $user = new User();
        $game = new Game();

        // WHEN
        $user->addFavoriteGame($game);
        $user->removeFavoriteGame($game);

        // THEN
        $this->assertFalse($user->getFavoriteGames()->contains($game));
    }
}