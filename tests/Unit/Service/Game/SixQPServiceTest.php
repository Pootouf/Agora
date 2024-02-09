<?php

namespace Unit\Service\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
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

    public function testInitializeNewRoundValidWithValidGameExpectSuccess(): void
    {
        //GIVEN
        $game = $this->createGame(6, 4);
        $players = $game->getPlayerSixQPs();
        $rows = $game->getRowSixQPs();
        $expectedNumberOfCard = 1;
        //WHEN
        $this->sixQPService->initializeNewRound($game);
        //THEN
        foreach ($players as $player) {
            $this->assertNotNull($player->getCards());
            $this->assertSame(PlayerSixQP::$NUMBER_OF_CARDS_BY_PLAYER, count($player->getCards()));
        }
        foreach ($rows as $row) {
            $this->assertNotNull($row->getCards());
            $this->assertSame($expectedNumberOfCard, count($row->getCards()));
        }
    }

    public function testInitializeNewRoundInvalidWithNotEnoughPlayersExpectFailure(): void
    {
        //GIVEN
        $game = $this->createGame(1, 4);
        //THEN
        $this->expectException(Exception::class);
        //WHEN
        $this->sixQPService->initializeNewRound($game);
    }

    public function testInitializeNewRoundInvalidWithTooManyPlayers(): void
    {
        //GIVEN
        $game = $this->createGame(11, 4);
        //THEN
        $this->expectException(Exception::class);
        //WHEN
        $this->sixQPService->initializeNewRound($game);
    }

    public function testInitializeNewRoundInvalidWithInvalidNumberOfRowsExpectFailure(): void
    {
        //GIVEN
        $game = $this->createGame(8, 2);
        //THEN
        $this->expectException(Exception::class);
        //WHEN
        $this->sixQPService->initializeNewRound($game);
    }

    public function testChooseCardWhenCardNotOwnedExpectFailure(): void
    {
        //GIVEN
        $game = new GameSixQP();
        $player = new PlayerSixQP('test', $game);
        $card = new CardSixQP();
        //THEN
        $this->expectException(Exception::class);
        //WHEN
        $this->sixQPService->chooseCard($player, $card);
    }

    public function testChooseCardWhenPlayerAlreadyChoseExpectFailure(): void
    {
        //GIVEN
        $game = new GameSixQP();
        $player = new PlayerSixQP('test', $game);
        $oldCard = new CardSixQP();
        $newCard = new CardSixQP();
        $player -> addCard($oldCard);
        $player -> addCard($newCard);
        $this->sixQPService->chooseCard($player, $oldCard);
        //THEN
        $this->expectException(Exception::class);
        //WHEN
        $this->sixQPService->chooseCard($player, $newCard);
    }

    public function testChooseCardWhenCardOwnedAndPlayerDontAlreadyHaveChosenExpectSuccess(): void
    {
        //GIVEN
        $card = new CardSixQP();
        $card->setValue(1);
        $card->setPoints(1);
        $game = new GameSixQP();
        $player = new PlayerSixQP('test', $game);
        $player->addCard($card);
        $player->setGame($game);
        $player->setUsername("test");
        $cards = $player->getCards();
        //WHEN
        $this->sixQPService->chooseCard($player, $card);
        //THEN
        $this->assertFalse($cards->contains($card));
        $this->assertNotNull($player->getChosenCardSixQP());
        $this->assertFalse($cards->contains($card));

    }


    public function testPlaceCardIntoRowWith4CardIntoRowExpect0Code()
    {
        //GIVEN
        $game = $this->createGame(4, 4);
        $player = $game->getPlayerSixQPs()->first();
        $card = new CardSixQP();
        $card->setValue(12);
        $chosenCard = new ChosenCardSixQP($player, $game, $card, true);
        $row = $game->getRowSixQPs()->first();
        for ($i = 0; $i < 4; $i++) {
            $c = new CardSixQP();
            $row->addCard($c);
        }
        $expectedNumberOfCard = 5;
        $expectedResult = 0;
        //WHEN
        $result = $this->sixQPService->placeCardIntoRow($chosenCard, $row);
        //THEN
        $this->assertEquals($expectedResult, $result);
        $this->assertEquals($expectedNumberOfCard, $row->getCards()->count());
        $this->assertEquals($card->getValue(), $row->getCards()->last()->getValue());
    }

    public function testPlaceCardIntoRowWith5CardIntoRowExpectNegativeCode()
    {
        //GIVEN
        $game = $this->createGame(4, 4);
        $player = $game->getPlayerSixQPs()->first();
        $card = new CardSixQP();
        $card->setValue(12);
        $chosenCard = new ChosenCardSixQP($player, $game, $card, true);
        $row = $game->getRowSixQPs()->first();
        for ($i = 0; $i < 5; $i++) {
            $c = new CardSixQP();
            $c->setPoints(1);
            $row->addCard($c);
        }
        $expectedNumberOfCard = 1;
        $expectedResult = -1;
        //WHEN
        $result = $this->sixQPService->placeCardIntoRow($chosenCard, $row);
        //THEN
        $this->assertEquals($expectedResult, $result);
        $this->assertEquals($expectedNumberOfCard, $row->getCards()->count());
        $this->assertEquals($card->getValue(), $row->getCards()->last()->getValue());
    }

    public function testRankingWith11PlayersExpectFailure()
    {
        //GIVEN
        $game = $this->createGame(11, 4);
        //THEN
        $this->expectException(Exception::class);
        //WHEN
        $this->sixQPService->getRanking($game);
    }

    public function testRankingWith4PlayersExpectValidRanking()
    {
        //GIVEN
        $game = $this->createGame(4, 4);
        $players = $game->getPlayerSixQPs();
        $player0 = $players->get(0);
        $player0->getDiscardSixQP()->addPoints(47);
        $player1 = $players->get(1);
        $player1->getDiscardSixQP()->addPoints(23);
        $player2 = $players->get(2);
        $player2->getDiscardSixQP()->addPoints(2);
        $player3 = $players->get(3);
        $player3->getDiscardSixQP()->addPoints(33);
        //WHEN
        $ranking = $this->sixQPService->getRanking($game);
        //THEN
        $this->assertEquals($player2->getUsername(), $ranking[0]->getUsername());
        $this->assertEquals($player1->getUsername(), $ranking[1]->getUsername());
        $this->assertEquals($player3->getUsername(), $ranking[2]->getUsername());
        $this->assertEquals($player0->getUsername(), $ranking[3]->getUsername());
    }

    public function testCalculatePointsShouldSuccessWithPlayerWith3Points() : void
    {
        //GIVEN
        $expectedPoint = 3;
        $game = $this->createGame(4, 4);
        $player = $game->getPlayerSixQPs()->first();
        $card = new CardSixQP();
        $card->setPoints($expectedPoint);
        $player->getDiscardSixQP()->addCard($card);
        //WHEN
        $this->sixQPService->calculatePoints($player->getDiscardSixQP());
        //THEN
        $this->assertTrue($player->getDiscardSixQP()->getTotalPoints() == $expectedPoint);
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
            $player = new PlayerSixQP('test'.$i, $game);
            $player->setDiscardSixQP(new DiscardSixQP($player, $game));
            $game->addPlayerSixQP($player);
        }
        for ($i = 0; $i < $numberOfRow; $i++) {
            $row = new RowSixQP();
            $game->addRowSixQP($row);
        }
        return $game;
    }
}
