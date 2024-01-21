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


    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $cardSixQPRepository = $this->createMock(CardSixQPRepository::class);
        $cards = [];
        for($i = 0; $i < 104; $i++) {
            $cards[] = new CardSixQP();
        }
        $cardSixQPRepository
            ->method('findAll')
            ->willReturn($cards);

        $this->sixQPService = new SixQPService($entityManager, $cardSixQPRepository);
    }


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
        $this->assertFalse($player->getChosenCardSixQP()->isVisible());
        $cards = $player->getCards();
        $this->assertFalse($cards->contains($card));

    }

    public function testInitializeNewRoundValidWithValidGame(): void
    {
        $game = $this->createGame(6, 4);
        $this->sixQPService->initializeNewRound($game);
        $players = $game->getPlayerSixQPs();
        $rows = $game->getRowSixQPs();
        foreach ($players as $player) {
            $this->assertNotNull($player->getCards());
            $this->assertSame(PlayerSixQP::$NUMBER_OF_CARDS_BY_PLAYER, count($player->getCards()));
        }
        foreach ($rows as $row) {
            $this->assertNotNull($row->getCards());
            $this->assertSame(1, count($row->getCards()));
        }
    }

    public function testInitializeNewRoundInvalidWithNotEnoughPlayers(): void
    {
        $game = $this->createGame(1, 4);
        $this->expectException(Exception::class);
        $this->sixQPService->initializeNewRound($game);
    }

    public function testInitializeNewRoundInvalidWithTooManyPlayers(): void
    {
        $game = $this->createGame(15, 4);
        $this->expectException(Exception::class);
        $this->sixQPService->initializeNewRound($game);
    }

    public function testInitializeNewRoundInvalidWithInvalidNumberOfRows(): void
    {
        $game = $this->createGame(8, 2);
        $this->expectException(Exception::class);
        $this->sixQPService->initializeNewRound($game);
    }


    private function createGame(int $numberOfPlayer, int $numberOfRow): GameSixQP
    {
        $game = new GameSixQP();
        for ($i = 0; $i < $numberOfPlayer; $i++) {
            $player = new PlayerSixQP('test', $game);
            $game->addPlayerSixQP($player);
        }
        for ($i = 0; $i < $numberOfRow; $i++) {
            $row = new RowSixQP();
            $game->addRowSixQP($row);
        }
        return $game;
    }
}
