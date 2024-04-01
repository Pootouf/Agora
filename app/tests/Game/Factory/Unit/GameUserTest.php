<?php

namespace App\Tests\Game\Factory\Unit;

use App\Entity\Game\GameUser;
use PHPUnit\Framework\TestCase;

class GameUserTest extends TestCase
{
    private GameUser $gameUser;

    public function testInit()
    {
        //GIVEN
        $role = 'ROLE_USER';
        //WHEN
        // setUp()

        //THEN
        $this->assertTrue($this->gameUser->getId() >= 0);
        $this->assertContains($role, $this->gameUser->getRoles());
    }

    public function testSetUsername(): void
    {
        //GIVEN
        $username = "pseudo";
        //WHEN
        $this->gameUser->setUsername($username);
        //THEN
        $this->assertEquals($username, $this->gameUser->getUsername());
        $this->assertEquals($username, $this->gameUser->getUserIdentifier());
    }

    public function testSetRoles(): void
    {
        //GIVEN
        $roles = ["hahaha", "zzhahza"];
        $role_user = 'ROLE_USER';
        //WHEN
        $this->gameUser->setRoles($roles);
        //THEN
        foreach($roles as $role) {
            $this->assertContains($role, $this->gameUser->getRoles());
        }
        $this->assertContains($role_user, $this->gameUser->getRoles());
    }

    public function testSetPassword(): void
    {
        //GIVEN
        $password = "ceci est un mot de passe safe";
        //WHEN
        $this->gameUser->setPassword($password);
        //THEN
        $this->assertEquals($password, $this->gameUser->getPassword());
    }

    public function testEraseCredentials(): void
    {
        //GIVEN
        $user = clone $this->gameUser;
        //WHEN
        $this->gameUser->eraseCredentials();
        //THEN
        $this->assertEquals($user, $this->gameUser);
    }
    protected function setUp(): void
    {
        $this->gameUser = new GameUser();
    }
}