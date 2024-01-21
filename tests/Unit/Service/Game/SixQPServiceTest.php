<?php

namespace App\Tests\Service\Game;

use App\DataFixtures\SixQPFixtures;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\CardSixQPRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use App\Service\Game\SixQPService;

class SixQPServiceTest extends TestCase
{
    private SixQPService $sixQPService;
    // Unit Tests for chooseCard
    public function testChooseCardWhenCardNotOwned(): void
    {
        $game = new GameSixQP();
        $player = new PlayerSixQP('test', $game);
        $card = new CardSixQP();
        $this->expectException(Exception::class);
        $this->sixQPService->chooseCard($player, $card);
    }

    public function testChooseCardWhenPlayerAlreadyChose(): void
    {
        $game = new GameSixQP();
        $player = new PlayerSixQP('test', $game);
        $oldCard = new CardSixQP();
        $newCard = new CardSixQP();
        $this->expectException(Exception::class);
        $this->sixQPService->chooseCard($player, $oldCard);
        $this->sixQPService->chooseCard($player, $newCard);
    }

    public function testChooseCardWhenValid(): void
    {

        $card = new CardSixQP();
        $card->setValue(1);
        $card->setPoints(1);

        $game = new GameSixQP();

        $player = new PlayerSixQP('test', $game);
        $player->addCard($card);
        $player->setGame($game);
        $player->setUsername("test");
        $cards = $player->getCards();

        $this->assertTrue($cards->contains($card));
        $this->assertNull($player->getChosenCardSixQP());
        $this->sixQPService->chooseCard($player, $card);
        $this->assertNotNull($player->getChosenCardSixQP());
        $this->assertFalse($player->getChosenCardSixQP()->isState());
        $cards = $player->getCards();
        $this->assertFalse($cards->contains($card));

    }




    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $cardSixQPRepository = $this->createMock(CardSixQPRepository::class);
        $this->sixQPService = new SixQPService($entityManager, $cardSixQPRepository);
    }
}
