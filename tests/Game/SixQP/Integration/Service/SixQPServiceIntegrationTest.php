<?php


use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\SixQP\SixQPService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SixQPServiceIntegrationTest extends KernelTestCase
{

    public function testInitializeNewRoundValidWithValidGame(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);

        $game = $this->createGame(6, 4);
        $sixQPService->initializeNewRound($game);

        $gameRepository = static::getContainer()->get(GameSixQPRepository::class);
        $newGame = $gameRepository->findOneBy(['id' => $game->getId()]);
        $players = $newGame->getPlayerSixQPs();
        $rows = $newGame->getRowSixQPs();

        foreach ($players as $player) {
            $this->assertNotNull($player->getCards());
            $this->assertSame(PlayerSixQP::$NUMBER_OF_CARDS_BY_PLAYER, count($player->getCards()));
            $this->assertSame(count($game->getPlayerSixQPs()), count($players));
        }
        foreach ($rows as $row) {
            $this->assertNotNull($row->getCards());
            $this->assertSame(1, count($row->getCards()));
            $this->assertSame(count($game->getRowSixQPs()), count($rows));
        }
    }

    public function testInitializeNewRoundInvalidWithNotEnoughPlayers(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $game = $this->createGame(1, 4);
        $this->expectException(Exception::class);
        $sixQPService->initializeNewRound($game);
    }

    public function testInitializeNewRoundInvalidWithTooManyPlayers(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $game = $this->createGame(1, 4);
        $this->expectException(Exception::class);
        $sixQPService->initializeNewRound($game);
    }

    public function testInitializeNewRoundInvalidWithInvalidNumberOfRows(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $game = $this->createGame(4, 3);
        $this->expectException(Exception::class);
        $sixQPService->initializeNewRound($game);
    }

    public function testChooseCardWhenCardNotOwned(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $game = $this->createGame(4, 4);
        $player = new PlayerSixQP('test', $game);
        $card = new CardSixQP();
        $this->expectException(Exception::class);
        $sixQPService->chooseCard($player, $card);
    }

    public function testChooseCardWhenPlayerAlreadyChose(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(4, 4);
        $player = new PlayerSixQP('test', $game);
        $oldCard = new CardSixQP();
        $newCard = new CardSixQP();
        $oldCard->setValue(1);
        $oldCard->setPoints(1);
        $newCard->setValue(2);
        $newCard->setPoints(1);
        $player->addCard($oldCard);
        $player->addCard($newCard);
        $entityManager->persist($oldCard);
        $entityManager->persist($newCard);
        $entityManager->persist($game);
        $entityManager->persist($player);
        $sixQPService->chooseCard($player, $oldCard);
        $this->expectException(Exception::class);
        $sixQPService->chooseCard($player, $newCard);
    }

    public function testChooseCardWhenValid(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $card = new CardSixQP();
        $card->setValue(1);
        $card->setPoints(1);

        $game = $this->createGame(4, 4);

        $player = new PlayerSixQP('test', $game);
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
        $cards = $player->getCards();
        $this->assertNotNull($cards);
        $this->assertFalse($cards->contains($card));
        $this->assertNotNull($player->getChosenCardSixQP());
    }

    public function testIsGameEnded() : void {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player = $game->getPlayerSixQPs()[0];
        $player2 = $game->getPlayerSixQPs()[1];
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
        $entityManager->persist($card);
        $entityManager->persist($card2);
        $entityManager->persist($player);
        $entityManager->persist($player2);
        $entityManager->persist($game);
        $entityManager->persist($discard);
        $entityManager->persist($discard2);
        $entityManager->flush();
        $sixQPService->calculatePoints($discard);
        $sixQPService->calculatePoints($discard2);
        $this->assertFalse($sixQPService->isGameEnded($game));
        $player -> getDiscardSixQP() -> addPoints(65);
        $this->assertTrue($sixQPService->isGameEnded($game));
    }

    public function testcalculatePoints() : void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(4, 4);
        $player = new PlayerSixQP("test", $game);
        $discard = new DiscardSixQP($player, $game);
        $card = new CardSixQP();
        $card -> setValue(1);
        $card -> setPoints(1);
        $player->setDiscardSixQP($discard);
        $player->getDiscardSixQP()->addCard($card);
        $card2 = new CardSixQP();
        $card2 -> setValue(5);
        $card2 -> setPoints(13);
        $player->getDiscardSixQP()->addCard($card2);
        $entityManager->persist($card);
        $entityManager->persist($card2);
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->persist($discard);
        $entityManager->flush();
        $sixQPService->calculatePoints($discard);
        $this->assertTrue($player->getDiscardSixQP()->getTotalPoints() == 14);
    }

    public function testGetRanking() : void {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(4, 4);
        $player = $game->getPlayerSixQPs()[0];
        $player2 = $game->getPlayerSixQPs()[1];
        $player3 = $game->getPlayerSixQPs()[2];
        $player4 = $game->getPlayerSixQPs()[3];
        $discard = new DiscardSixQP($player, $game);
        $discard2 = new DiscardSixQP($player2, $game);
        $discard3 = new DiscardSixQP($player3, $game);
        $discard4 = new DiscardSixQP($player4, $game);
        $player->setDiscardSixQP($discard);
        $player2->setDiscardSixQP($discard2);
        $player3->setDiscardSixQP($discard3);
        $player4->setDiscardSixQP($discard4);
        $discard->addPoints(12);
        $discard2->addPoints(10);
        $discard3->addPoints(6);
        $discard4->addPoints(47);
        $entityManager->persist($player);
        $entityManager->persist($player2);
        $entityManager->persist($player3);
        $entityManager->persist($player4);
        $entityManager->persist($game);
        $entityManager->persist($discard);
        $entityManager->persist($discard2);
        $entityManager->persist($discard3);
        $entityManager->persist($discard4);
        $entityManager->flush();
        $result = $sixQPService->getRanking($game);
        $expectedResult = [$player3, $player2, $player, $player4];
        $i = 0;
        foreach($expectedResult as $player) {
            $this->assertEquals($player, $result[$i]);
            ++$i;
        }
    }

    public function testDoesAllPlayerHaveChosenShouldReturnTrue() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player1 = $game->getPlayerSixQPs()->first();
        $player2 = $game->getPlayerSixQPs()->last();
        $card1 = new CardSixQP();
        $card1->setValue(12);
        $card1->setPoints(12);
        $player1->addCard($card1);
        $card2 = new CardSixQP();
        $player2->addCard($card2);
        $card2->setValue(12);
        $card2->setPoints(12);
        $entityManager->persist($card1);
        $entityManager->persist($card2);
        $entityManager->persist($player2);
        $entityManager->persist($player1);
        $entityManager->persist($game);
        $entityManager->flush();
        $sixQPService->chooseCard($player1, $card1);
        $sixQPService->chooseCard($player2, $card2);
        // WHEN
        $result = $sixQPService->doesAllPlayersHaveChosen($game);
        // THEN
        $this->assertTrue($result);
    }

    public function testDoesAllPlayerHaveChosenShouldReturnFalse() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player1 = $game->getPlayerSixQPs()->first();
        $card1 = new CardSixQP();
        $card1->setValue(12);
        $card1->setPoints(12);
        $player1->addCard($card1);
        $entityManager->persist($card1);
        $entityManager->persist($player1);
        $entityManager->persist($game);
        $entityManager->flush();
        $sixQPService->chooseCard($player1, $card1);
        // WHEN
        $result = $sixQPService->doesAllPlayersHaveChosen($game);
        // THEN
        $this->assertFalse($result);
    }

    public function testDoesPlayerAlreadyHasPlayedShouldReturnFalse() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player1 = $game->getPlayerSixQPs()->first();
        $card1 = new CardSixQP();
        $card1->setValue(12);
        $card1->setPoints(12);
        $player1->addCard($card1);
        $entityManager->persist($card1);
        $entityManager->persist($player1);
        $entityManager->persist($game);
        $entityManager->flush();
        // WHEN
        $result = $sixQPService->doesPlayerAlreadyHasPlayed($player1);
        // THEN
        $this->assertFalse($result);
    }

    public function testDoesPlayerAlreadyHasPlayedShouldReturnTrue() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player1 = $game->getPlayerSixQPs()->first();
        $card1 = new CardSixQP();
        $card1->setValue(12);
        $card1->setPoints(12);
        $player1->addCard($card1);
        $entityManager->persist($card1);
        $entityManager->persist($player1);
        $entityManager->persist($game);
        $entityManager->flush();
        $sixQPService->chooseCard($player1, $card1);
        // WHEN
        $result = $sixQPService->doesPlayerAlreadyHasPlayed($player1);
        // THEN
        $this->assertTrue($result);
    }

    public function testGetNotPlacedCardShouldReturnTwoCards() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player1 = $game->getPlayerSixQPs()->first();
        $player2 = $game->getPlayerSixQPs()->last();
        $card1 = new CardSixQP();
        $card1->setValue(12);
        $card1->setPoints(12);
        $player1->addCard($card1);
        $card2 = new CardSixQP();
        $player2->addCard($card2);
        $card2->setValue(12);
        $card2->setPoints(12);
        $entityManager->persist($card1);
        $entityManager->persist($card2);
        $entityManager->persist($player2);
        $entityManager->persist($player1);
        $entityManager->persist($game);
        $entityManager->flush();
        $sixQPService->chooseCard($player1, $card1);
        $sixQPService->chooseCard($player2, $card2);
        $expectedResult = [$player1->getChosenCardSixQP(), $player2->getChosenCardSixQP()];
        //WHEN
        $result = $sixQPService->getNotPlacedCard($game);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetNotPlacedCardShouldReturnOneCard() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player1 = $game->getPlayerSixQPs()->first();
        $player2 = $game->getPlayerSixQPs()->last();
        $card1 = new CardSixQP();
        $card1->setValue(12);
        $card1->setPoints(12);
        $player1->addCard($card1);
        $card2 = new CardSixQP();
        $player2->addCard($card2);
        $card2->setValue(12);
        $card2->setPoints(12);
        $row = $game->getRowSixQPs()->first();
        $entityManager->persist($card1);
        $entityManager->persist($card2);
        $entityManager->persist($player2);
        $entityManager->persist($player1);
        $entityManager->persist($row);
        $entityManager->persist($game);
        $entityManager->flush();
        $sixQPService->chooseCard($player1, $card1);
        $sixQPService->chooseCard($player2, $card2);
        $row->addCard($card2);
        $expectedResult = [$player1->getChosenCardSixQP()];
        //WHEN
        $result = $sixQPService->getNotPlacedCard($game);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testIsGameEndedWhenAPlayerStillHasACardShouldReturnFalse() : void {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player = $game->getPlayerSixQPs()->first();
        $card = new CardSixQP();
        $card -> setValue(1);
        $card -> setPoints(1);
        $player->addCard($card);
        $entityManager->persist($game);
        $entityManager->persist($player);
        $entityManager->persist($card);
        $entityManager->flush();
        //WHEN
        $result = $sixQPService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }

    public function testIsGameEndedWhenNoPlayerReachedLimitShouldReturnFalse() : void {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player = $game->getPlayerSixQPs()->first();
        $player2 = $game->getPlayerSixQPs()->last();
        $discard = new DiscardSixQP($player, $game);
        $player->setDiscardSixQP($discard);
        $discard2 = new DiscardSixQP($player2, $game);
        $player2->setDiscardSixQP($discard2);
        $entityManager->persist($game);
        $entityManager->persist($player);
        $entityManager->persist($player2);
        $entityManager->persist($discard);
        $entityManager->persist($discard2);
        $entityManager->flush();
        $player->getDiscardSixQP()->addPoints($sixQPService::$MAX_POINTS - 1);
        $entityManager->persist($game);
        $entityManager->persist($player);
        //WHEN
        $result = $sixQPService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }

    public function testIsGameEndedWhenPlayerReachedLimitShouldReturnTrue() : void {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player = $game->getPlayerSixQPs()->first();
        $player2 = $game->getPlayerSixQPs()->last();
        $discard = new DiscardSixQP($player, $game);
        $player->setDiscardSixQP($discard);
        $discard2 = new DiscardSixQP($player2, $game);
        $player2->setDiscardSixQP($discard2);
        $entityManager->persist($game);
        $entityManager->persist($player);
        $entityManager->persist($player2);
        $entityManager->persist($discard);
        $entityManager->persist($discard2);
        $entityManager->flush();
        $player->getDiscardSixQP()->addPoints($sixQPService::$MAX_POINTS);
        $entityManager->persist($game);
        $entityManager->persist($player);
        //WHEN
        $result = $sixQPService->isGameEnded($game);
        //THEN
        $this->assertTrue($result);
    }

    public function testGetPlayerFromNameAndGameShouldReturnGoodPlayer() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player = $game->getPlayerSixQPs()->first();
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->flush();
        $expectedResult = $player;
        //WHEN
        $result = $sixQPService->getPlayerFromNameAndGame($game, $player->getUsername());
        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetPlayerFromNameAndGameShouldReturnNothing() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player = $game->getPlayerSixQPs()->first();
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->flush();
        $expectedResult = null;
        $wrongName = "gtaeuaioea";
        //WHEN
        $result = $sixQPService->getPlayerFromNameAndGame($game, $wrongName);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testHasCardLeftShouldReturnFalse() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $players = $game->getPlayerSixQPs();
        //WHEN
        $result = $sixQPService->hasCardLeft($players);
        //THEN
        $this->assertFalse($result);
    }

    public function testHasCardLeftShouldReturnTrue() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $players = $game->getPlayerSixQPs();
        $player2 = $game->getPlayerSixQPs()->last();
        $card = new CardSixQP();
        $card->setPoints(12);
        $card->setValue(1);
        $player2->addCard($card);
        $entityManager->persist($card);
        $entityManager->flush();
        //WHEN
        $result = $sixQPService->hasCardLeft($players);
        //THEN
        $this->assertTrue($result);
    }

    public function testGetWinnerShouldReturnNull() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(3, 4);
        $players = $game->getPlayerSixQPs();
        $player1 = $players->first();
        $player2 = $players[1];
        $player3 = $players->last();
        $discard1 = new DiscardSixQP($player1, $game);
        $player1->setDiscardSixQP($discard1);
        $discard1->addPoints(10);
        $discard2 = new DiscardSixQP($player2, $game);
        $player2->setDiscardSixQP($discard2);
        $discard2->addPoints(10);
        $discard3 = new DiscardSixQP($player3, $game);
        $player3->setDiscardSixQP($discard3);
        $discard3->addPoints(SixQPService::$MAX_POINTS);
        $entityManager->persist($discard1);
        $entityManager->persist($discard2);
        $entityManager->persist($discard3);
        $entityManager->flush();
        //WHEN
        $result = $sixQPService->getWinner($game);
        //THEN
        $this->assertNull($result);
    }

    public function testGetWinnerShouldReturnPlayer2() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(3, 4);
        $players = $game->getPlayerSixQPs();
        $player1 = $players->first();
        $player2 = $players[1];
        $player3 = $players->last();
        $discard1 = new DiscardSixQP($player1, $game);
        $player1->setDiscardSixQP($discard1);
        $discard1->addPoints(11);
        $discard2 = new DiscardSixQP($player2, $game);
        $player2->setDiscardSixQP($discard2);
        $discard2->addPoints(10);
        $discard3 = new DiscardSixQP($player3, $game);
        $player3->setDiscardSixQP($discard3);
        $discard3->addPoints(SixQPService::$MAX_POINTS);
        $entityManager->persist($discard1);
        $entityManager->persist($discard2);
        $entityManager->persist($discard3);
        $entityManager->flush();
        //WHEN
        $result = $sixQPService->getWinner($game);
        //THEN
        $this->assertSame($player2, $result);
    }

    public function testClearCardsShouldSucceed() : void
    {
        //GIVEN
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2, 4);
        $player = $game->getPlayerSixQPs()->first();
        $discard = new DiscardSixQP($player, $game);
        $player->setDiscardSixQP($discard);
        $card = new CardSixQP();
        $card->setValue(1);
        $card->setPoints(1);
        $player->addCard($card);
        $entityManager->persist($discard);
        $entityManager->persist($card);
        $entityManager->flush();
        $sixQPService->chooseCard($player, $card);
        $player2 = $game->getPlayerSixQPs()->last();
        $discard2 = new DiscardSixQP($player2, $game);
        $player2->setDiscardSixQP($discard2);
        $card2 = new CardSixQP();
        $card2->setValue(2);
        $card2->setPoints(1);
        $player2->addCard($card2);
        $entityManager->persist($discard2);
        $entityManager->persist($card2);
        $entityManager->flush();
        $sixQPService->chooseCard($player2, $card2);
        $array = [$player->getChosenCardSixQP(), $player2->getChosenCardSixQP()];
        $expectedResult = [null, null];
        //WHEN
        $sixQPService->clearCards($array);
        //THEN
        $this->assertSame($expectedResult,
            [$player->getChosenCardSixQP(), $player2->getChosenCardSixQP()]);

    }
    private function createGame(int $numberOfPlayer, int $numberOfRow): GameSixQP
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = new GameSixQP();
        $game->setGameName(AbstractGameManagerService::$SIXQP_LABEL);
        for ($i = 0; $i < $numberOfPlayer; $i++) {
            $player = new PlayerSixQP('test', $game);
            $game->addPlayerSixQP($player);
            $entityManager->persist($player);
        }
        for ($i = 0; $i < $numberOfRow; $i++) {
            $row = new RowSixQP();
            $row->setPosition($i);
            $game->addRowSixQP($row);
            $entityManager->persist($row);
        }
        $entityManager->persist($game);
        $entityManager->flush();
        return $game;
    }

}
