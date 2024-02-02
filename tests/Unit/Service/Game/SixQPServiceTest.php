<?php

namespace Unit\Service\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\CardSixQPRepository;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\SixQP\SixQPService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class SixQPServiceTest extends TestCase
{
    private SixQPService $sixQPService;


    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $cardSixQPRepository = $this->createMock(CardSixQPRepository::class);
        $chosenCardSixQPRepository = $this->createMock(ChosenCardSixQPRepository::class);
        $playerSixQPRepository = $this->createMock(PlayerSixQPRepository::class);
        $cards = [];
        for($i = 0; $i < 104; $i++) {
            $cards[] = new CardSixQP();
        }
        $cardSixQPRepository
            ->method('findAll')
            ->willReturn($cards);

        $this->sixQPService = new SixQPService($entityManager, $cardSixQPRepository,
            $chosenCardSixQPRepository, $playerSixQPRepository);
    }

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
        $player -> addCard($oldCard);
        $player -> addCard($newCard);
        $this->sixQPService->chooseCard($player, $oldCard);
        $this->expectException(Exception::class);
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

    public function testcalculatePoints() : void
    {
        $game = new GameSixQP();
        $player = new PlayerSixQP('test', $game);
        $discard = new DiscardSixQP($player, $game);
        $card = new CardSixQP();
        $player->setDiscardSixQP($discard);
        $card->setValue(5);
        $card->setPoints(3);
        $player->getDiscardSixQP()->addCard($card);
        $this->sixQPService->calculatePoints($discard);
        $this->assertTrue($player->getDiscardSixQP()->getTotalPoints() == 3);
        $card2 = new CardSixQP();
        $card2->setValue(104);
        $card2->setPoints(1);
        $player->getDiscardSixQP()->addCard($card2);
        $this->sixQPService->calculatePoints($discard);
        $this->assertTrue($player->getDiscardSixQP()->getTotalPoints() == 4);
    }

    public function testIsGameEnded() : void {
        $game = new GameSixQP();
        $player = new PlayerSixQP("test", $game);
        $player2 = new PlayerSixQP("test", $game);
        $game->addPlayerSixQP($player);
        $game->addPlayerSixQP($player2);
        $card = new CardSixQP();
        $card -> setValue(1);
        $card -> setPoints(1);
        $card2 = new CardSixQP();
        $card2 -> setValue(2);
        $card2 -> setPoints(2);
        $discard = new DiscardSixQP($player, $game);
        $discard2 = new DiscardSixQP($player2, $game);
        $player->setDiscardSixQP($discard);
        $player2->setDiscardSixQP($discard2);
        $player->getDiscardSixQP()->addCard($card);
        $player2->getDiscardSixQP()->addCard($card2);
        $this->sixQPService->calculatePoints($discard);
        $this->sixQPService->calculatePoints($discard2);
        $this->assertFalse($this->sixQPService->isGameEnded($game));
        $player -> getDiscardSixQP() -> addPoints(65);
        $this->assertTrue($this->sixQPService->isGameEnded($game));
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
