<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SixQPControllerTest extends WebTestCase
{
    /**
     * Test if all enclosures are shown and none send an error
     */
    public function testEnclosuresAreShownOnPageValid()
    {
        $player = $this->createClient();
        $crawler = $player->request('GET', '/six/q/p');
        $this->assertResponseStatusCodeSame(200);
    }


}