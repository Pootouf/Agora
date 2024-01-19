<?php

namespace App\Tests\Service\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\Game\SixQPService;

class SixQPServiceTest extends KernelTestCase
{

    // Unit Tests for chooseCard
    public function testChooseCardWhenCardNotOwned(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $player = new PlayerSixQP();
        $card = new CardSixQP();
        $this->expectException(Exception::class);
        $sixQPService->chooseCard($player, $card);
    }

    public function testChooseCardWhenPlayerAlreadyChose(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $player = new PlayerSixQP();
        $oldCard = new CardSixQP();
        $newCard = new CardSixQP();
        $this->expectException(Exception::class);
        $sixQPService->chooseCard($player, $oldCard);
        $sixQPService->chooseCard($player, $newCard);
    }

    public function testChooseCardWhenValid(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $card = new CardSixQP();
        $card->setValue(1);
        $card->setPoints(1);

        $game = new GameSixQP();

        $player = new PlayerSixQP();
        $player->addCard($card);
        $player->setGame($game);
        $player->setUsername("test");
        $cards = $player->getCards();

        $entityManager->persist($card);
        $entityManager->persist($game);
        $entityManager->persist($player);
        $entityManager->flush();

        $this->assertTrue($cards->contains($card));
        $this->assertNull($player->getChosenCardSixQP());
        $sixQPService->chooseCard($player, $card);
        $this->assertFalse($player->getChosenCardSixQP()->isState());
        $cards = $player->getCards();
        $this->assertFalse($cards->contains($card));
        $this->assertNotNull($player->getChosenCardSixQP());

    }


}
