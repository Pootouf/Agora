<?php

namespace App\Tests;

use App\Entity\Platform\Game;
use PHPUnit\Framework\TestCase;

class GameEntityTest extends TestCase
{


    public function testSetName()
    {
        // GIVEN
        $game = new Game();

        // WHEN
        $game->setName("6 qui prend");

        //THEN
        $this->assertEquals("6 qui prend", $game->getName());
    }

    public function testSetDescription()
    {
        // GIVEN
        $game = new Game();

        // WHEN
        $game->setDescrRule("le but du jeu est d'obtenir un 6 etc...");

        //THEN
        $this->assertEquals("le but du jeu est d'obtenir un 6 etc...", $game->getDescrRule());
    }

    public function testGetSetImgURL()
    {
        // GIVEN
        $game = new Game();

        // WHEN
        $game->setImgURL('https://example.com/image.jpg');

        //THEN
        $this->assertEquals('https://example.com/image.jpg', $entity->getImgURL());
    }

    public function testSetLabel()
    {
        // GIVEN
        $game = new Game();

        // WHEN
        $game->setLabel("6QP");

        //THEN
        $this->assertEquals("6QP" , $game->getLabel());
    }

    public function testSetIsActive()
    {
        // GIVEN
        $game1 = new Game(); // jeu ajouté dont l'état ne sera pas changé
        $game2 = new Game(); // jeu ajouté dont isActive sera true
        $game3 = new Game(); // jeu ajouté dont isActive sera false

        // WHEN
        $game2->setIsActive(true);
        $game3->setIsActive(false);


        //THEN
        $this->assertFalse($game3->isIsActive());
        $this->assertTrue($game2->isIsActive());
        $this->assertFalse($game3->isIsActive());

    }

    public function testSetMinPlayer()
    {
        // GIVEN
        $game = new Game();

        // WHEN
        $game->setMinPlayers(2);

        //THEN
        $this->assertEquals(2, $game->getMinPlayers());
    }

    public function testSetMaxPlayer()
    {
        // GIVEN
        $game = new Game();

        // WHEN
        $game->setMaxPlayers(8);

        //THEN
        $this->assertEquals(8, $game->getMaxPlayers());
    }
}
