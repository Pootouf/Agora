<?php

namespace App\Tests\Functional\Game;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SixQPControllerTest extends WebTestCase
{
    public function testEnclosuresAreShownOnPageValid(): void
    {
        $player = $this->createClient();
        $crawler = $player->request('GET', '/six/q/p');
        $this->assertResponseStatusCodeSame(200);
    }


}