<?php

namespace App\Tests\Game\Splendor\Unit\Service;

use App\Entity\Game\Splendor\CardCostSPL;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\DrawCardsSPLRepository;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use App\Repository\Game\Splendor\PlayerCardSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Repository\Game\Splendor\RowSPLRepository;
use App\Repository\Game\Splendor\TokenSPLRepository;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SPLServiceTest extends TestCase
{
    private SPLService $SPLService;

    private TokenSPLService $tokenSPLService;
    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $playerRepository = $this->createMock(PlayerSPLRepository::class);
        $rowSPLRepository = $this->createMock(RowSPLRepository::class);
        $playerCardRepository = $this->createMock(PlayerCardSPLRepository::class);
        $tokenRepository = $this->createMock(TokenSPLRepository::class);
        $nobleTileRepository = $this->createMock(NobleTileSPLRepository::class);
        $developmentCardRepository = $this->createMock(DevelopmentCardsSPLRepository::class);
        $drawCardRepository = $this->createMock(DrawCardsSPLRepository::class);
        $logger = $this->createMock(LoggerInterface::class);
        $this->SPLService = new SPLService($entityManager, $playerRepository, $rowSPLRepository,
            $nobleTileRepository, $developmentCardRepository, $playerCardRepository, $drawCardRepository,
            $logger);
        $this->tokenSPLService = new TokenSPLService($entityManager, $tokenRepository, $this->SPLService);
    }

    public function testTakeTokenWhenAlreadyFull() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoard();
        for ($i = 0; $i < 10; ++$i) {
            $personalBoard->addToken(new TokenSPL());
        }
        $this->assertSame(10, $personalBoard->getTokens()->count());
        $token = new TokenSPL();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->tokenSPLService->takeToken($player, $token);
    }

    public function testTakeThreeIdenticalTokens() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoard();
        $token1 = new TokenSPL();
        $token1->setColor("blue");
        $selectedToken1 = new SelectedTokenSPL();
        $selectedToken1->setToken($token1);
        $token2 = new TokenSPL();
        $token2->setColor("blue");
        $selectedToken2 = new SelectedTokenSPL();
        $selectedToken2->setToken($token2);
        $token3 = new TokenSPL();
        $token3->setColor("blue");
        $personalBoard->addSelectedToken($selectedToken1);
        $personalBoard->addSelectedToken($selectedToken2);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->tokenSPLService->takeToken($player, $token3);
    }

    public function testTakeThreeTokensButWithTwiceSameColor() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoard();
        $token1 = new TokenSPL();
        $token1->setColor("blue");
        $selectedToken1 = new SelectedTokenSPL();
        $selectedToken1->setToken($token1);
        $token2 = new TokenSPL();
        $token2->setColor("red");
        $selectedToken2 = new SelectedTokenSPL();
        $selectedToken2->setToken($token2);
        $token3 = new TokenSPL();
        $token3->setColor("blue");
        $personalBoard->addSelectedToken($selectedToken1);
        $personalBoard->addSelectedToken($selectedToken2);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->tokenSPLService->takeToken($player, $token3);
    }

    public function testTakeFourTokens() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoard();
        $token1 = new TokenSPL();
        $token1->setColor("blue");
        $selectedToken1 = new SelectedTokenSPL();
        $selectedToken1->setToken($token1);
        $token2 = new TokenSPL();
        $token2->setColor("red");
        $selectedToken2 = new SelectedTokenSPL();
        $selectedToken2->setToken($token2);
        $token3 = new TokenSPL();
        $token3->setColor("green");
        $selectedToken3 = new SelectedTokenSPL();
        $selectedToken3->setToken($token3);
        $token4 = new TokenSPL();
        $token4->setColor("yellow");
        $personalBoard->addSelectedToken($selectedToken1);
        $personalBoard->addSelectedToken($selectedToken2);
        $personalBoard->addSelectedToken($selectedToken3);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->tokenSPLService->takeToken($player, $token4);
    }

    public function testTakeTokensWithTwoSameColorShouldFailBecauseNotAvailable() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $mainBoard = $game->getMainBoard();
        $token = new TokenSPL();
        $token->setColor("red");
        $mainBoard->addToken($token);
        $token1 = new TokenSPL();
        $token1->setColor("red");
        $mainBoard->addToken($token1);
        $this->tokenSPLService->takeToken($player, $token);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->tokenSPLService->takeToken($player, $token1);
    }

    public function testClearSelectedTokens() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoard();
        $token = new TokenSPL();
        $selectedToken = new SelectedTokenSPL();
        $selectedToken->setToken($token);
        $personalBoard->addSelectedToken($selectedToken);
        //WHEN
        $this->tokenSPLService->clearSelectedTokens($player);
        //THEN
        $this->assertEmpty($player->getPersonalBoard()->getSelectedTokens());
    }
    public function testIsGameEndedShouldReturnFalseBecauseNotLastPlayer() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        //WHEN
        $result = $this->SPLService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }
    public function testIsGameEndedShouldReturnFalseBecauseNotReachedLimit() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(false);
        $player2 = $game->getPlayers()->last();
        $player2->setTurnOfPlayer(true);
        $nobleTile = new NobleTileSPL();
        $nobleTile->setPrestigePoints(SplendorParameters::$MAX_PRESTIGE_POINTS - 1);
        $player2->getPersonalBoard()->addNobleTile($nobleTile);
        //WHEN
        $result = $this->SPLService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }

    public function testIsGameEndedShouldReturnTrue() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player2 = $game->getPlayers()->last();
        $player2->setTurnOfPlayer(true);
        $nobleTile = new NobleTileSPL();
        $nobleTile->setPrestigePoints(SplendorParameters::$MAX_PRESTIGE_POINTS);
        $player2->getPersonalBoard()->addNobleTile($nobleTile);
        $this->SPLService->calculatePrestigePoints($player2);
        //WHEN
        $result = $this->SPLService->isGameEnded($game);
        //THEN
        $this->assertTrue($result);
    }

    public function testIsGameEndedShouldReturnFalseBecauseReachedLimitButNotLastPlayer() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $nobleTile = new NobleTileSPL();
        $nobleTile->setPrestigePoints(SplendorParameters::$MAX_PRESTIGE_POINTS);
        $player->getPersonalBoard()->addNobleTile($nobleTile);
        //WHEN
        $result = $this->SPLService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }

    public function testGetRanking(): void
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $player2 = $game->getPlayers()->last();
        $nobleTile1 = new NobleTileSPL();
        $nobleTile1->setPrestigePoints(2);
        $player->getPersonalBoard()->addNobleTile($nobleTile1);
        $nobleTile2 = new NobleTileSPL();
        $nobleTile2->setPrestigePoints(3);
        $player2->getPersonalBoard()->addNobleTile($nobleTile2);
        $expectedRanking = array($player2, $player);
        $this->SPLService->calculatePrestigePoints($player);
        $this->SPLService->calculatePrestigePoints($player2);
        // WHEN
        $result = $this->SPLService->getRanking($game);
        // THEN
        $this->assertEquals($expectedRanking, $result);
    }

    public function testEndRoundOfPlayerWhenNotLastPlayer() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $player2 = $game->getPlayers()->last();
        $player2->setTurnOfPlayer(false);
        $game->addPlayer($player2);
        $expectedResult = [false, true];
        // WHEN
        $this->SPLService->endRoundOfPlayer($game, $player);
        // THEN
        $result = Array();
        foreach ($game->getPlayers() as $tmp) {
            $result[] = $tmp->isTurnOfPlayer();
        }
        $this->assertSame($expectedResult, $result);
    }

    public function testEndRoundOfPlayerWhenLastPlayer() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(false);        $player2 = $game->getPlayers()->last();
        $player2->setTurnOfPlayer(true);
        $game->addPlayer($player2);
        $expectedResult = [true, false];
        // WHEN
        $this->SPLService->endRoundOfPlayer($game, $player2);
        // THEN
        $result = Array();
        foreach ($game->getPlayers() as $tmp) {
            $result[] = $tmp->isTurnOfPlayer();
        }
        $this->assertSame($expectedResult, $result);
    }

    public function testBuyCardWhenPlayerHasEnoughMoneyChangePoints()
    {
        // GIVEN

        $game = $this->createGame(SplendorParameters::$MIN_NUMBER_OF_PLAYER);
        $player = $game->getPlayers()->first();
        $lastPoints = $player->getScore();

        $level = 3;
        $mainBoard = $game->getMainBoard();
        $card = $mainBoard->getDrawCards()->get($level - 1)->getDevelopmentCards()->last();
        $card->setPoints(3);

        // THEN

        $this->SPLService->buyCard($player, $card);

        // WHEN

        $this->assertNotSame($player->getScore(), $lastPoints);
    }

    public function testBuyReservedCardWhenPlayerHasEnoughMoneyChangePoints()
    {
        // GIVEN

        $game = $this->createGame(SplendorParameters::$MIN_NUMBER_OF_PLAYER);
        $player = $game->getPlayers()->first();
        $lastPoints = $player->getScore();

        $level = 3;
        $mainBoard = $game->getMainBoard();
        $card = $mainBoard->getDrawCards()->get($level - 1)->getDevelopmentCards()->last();
        $card->setPoints(3);
        $playerCard = new PlayerCardSPL($player, $card, true);
        $player->getPersonalBoard()->addPlayerCard($playerCard);

        // THEN

        $this->SPLService->buyCard($player, $card);

        // WHEN

        $this->assertNotSame($player->getScore(), $lastPoints);
    }

    public function testAssignNobleTileWhenPlayerHasEnoughMoneyChangePoints()
    {
        // GIVEN

        $game = $this->createGame(SplendorParameters::$MIN_NUMBER_OF_PLAYER);
        $mainBoard = $game->getMainBoard();
        $player = $game->getPlayers()->first();
        $personal = $player->getPersonalBoard();
        $lastPoints = $player->getScore();

        $price = 3;
        $noble = $this->createNobleTile(array(SplendorParameters::$COLOR_BLUE => $price));
        $mainBoard->addNobleTile($noble);

        $color = SplendorParameters::$COLOR_BLUE;
        for ($i = 0; $i < $price; $i++) {
            $row = $mainBoard->getRowsSPL()->first();
            $card = $row->getDevelopmentCards()->first();
            $card->setColor($color);
            $playerCard = new PlayerCardSPL($player, $card, false);
            $personal->addPlayerCard($playerCard);
        }

        // THEN

        $addingNoble = $this->SPLService->addBuyableNobleTilesToPlayer($game, $player);

        // WHEN

        $this->assertNotSame(-1, $addingNoble);
        $this->assertNotNull($addingNoble);
        $this->assertSame($player->getScore(),
            $lastPoints + SplendorParameters::$POINT_PRESTIGE_BY_NOBLE_TILE);
    }

    public function testBuyCardWhenNotEnoughMoney()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->SPLService->buyCard($player, $developmentCard);
    }
    public function testTokenRetrievedWhenBuy()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_RED);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $developmentCard->setLevel(1);
        // WHEN
        $this->SPLService->buyCard($player, $developmentCard);
        // THEN
        $this->assertNotContains($token, $player->getPersonalBoard()->getTokens());
    }

    public function testGoldTokenRetrievedWhenBuy()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_YELLOW);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $developmentCard->setLevel(1);
        // WHEN
        $this->SPLService->buyCard($player, $developmentCard);
        // THEN
        $this->assertNotContains($token, $player->getPersonalBoard()->getTokens());
    }

    public function testTokenNotRetrievedWhenPlayerHasCardOfGoodTypes()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_RED);
        $player->getPersonalBoard()->addToken($token);
        $playerDevCard = new DevelopmentCardsSPL();
        $playerDevCard->setColor(SplendorParameters::$COLOR_RED);
        $playerCard = new PlayerCardSPL($player, $playerDevCard, false);
        $player->getPersonalBoard()->addPlayerCard($playerCard);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $developmentCard->setLevel(1);
        $playerTokensBefore = $player->getPersonalBoard()->getTokens();
        // WHEN
        $this->SPLService->buyCard($player, $developmentCard);
        // THEN
        $this->assertEquals($playerTokensBefore, $player->getPersonalBoard()->getTokens());
    }

    public function testAddBuyableNobleTilesToPlayerShouldAddTileToPlayer() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        for ($i = 0; $i < 3; $i++) {
            $playerCard = $this->createPlayerCard($player, SplendorParameters::$COLOR_RED);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
            $playerCard = $this->createPlayerCard($player, SplendorParameters::$COLOR_BLUE);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
        }
        $nobleTile = $this->createNobleTile([
            SplendorParameters::$COLOR_RED => 3,
            SplendorParameters::$COLOR_BLUE => 3,
        ]);
        $game->getMainBoard()->addNobleTile($nobleTile);
        //WHEN
        $this->SPLService->addBuyableNobleTilesToPlayer($game, $player);
        //THEN
        $this->assertSame($nobleTile, $player->getPersonalBoard()->getNobleTiles()->first());
    }


    public function testAddBuyableNobleTilesToPlayerShouldDoNothing() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        for ($i = 0; $i < 3; $i++) {
            $playerCard = $this->createPlayerCard($player, SplendorParameters::$COLOR_RED);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
            $playerCard = $this->createPlayerCard($player, SplendorParameters::$COLOR_BLUE);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
        }
        $nobleTile = $this->createNobleTile([
            SplendorParameters::$COLOR_RED => 3,
            SplendorParameters::$COLOR_BLUE => 4,
        ]);
        $game->getMainBoard()->addNobleTile($nobleTile);
        $expectedNumberOfNobleTile = 0;
        //WHEN
        $this->SPLService->addBuyableNobleTilesToPlayer($game, $player);
        //THEN
        $this->assertSame($expectedNumberOfNobleTile, $player->getPersonalBoard()->getNobleTiles()->count());
    }

    public function testReserveCardFromMainBoardWhenIsAccessibleFromDiscardWithoutToken() : void
    {
        // GIVEN
        $game = $this->createGame(SplendorParameters::$MIN_NUMBER_OF_PLAYER);
        $player = $game->getPlayers()->first();
        $personal = $player->getPersonalBoard();

        while ($personal->getTokens()->count() !=
            SplendorParameters::$PLAYER_MAX_TOKEN)
        {
            $token = new TokenSPL();
            $token->setColor("red");
            $player->getPersonalBoard()->addToken($token);
        }

        $level = SplendorParameters::$DEVELOPMENT_CARD_LEVEL_ONE;
        $discard = $game->getMainBoard()->getDrawCards()->get($level);
        $card = $discard->getDevelopmentCards()->last();

        // WHEN

        $this->SPLService->reserveCard($player, $card);

        // THEN

        $this->assertNotContains($card, $game->getMainBoard()
            ->getDrawCards()->get($level)
            ->getDevelopmentCards());
        $this->assertSame(0,
            $this->SPLService->getNumberOfTokenAtColor($personal->getTokens(),
                SplendorParameters::$LABEL_JOKER));
    }

    public function testReserveCardFromMainBoardWhenIsAccessibleFromDiscardWithToken() : void
    {
        // GIVEN
        $game = $this->createGame(SplendorParameters::$MIN_NUMBER_OF_PLAYER);
        $player = $game->getPlayers()->first();
        $personal = $player->getPersonalBoard();

        $level = SplendorParameters::$DEVELOPMENT_CARD_LEVEL_ONE;
        $discard = $game->getMainBoard()->getDrawCards()->get($level);
        $card = $discard->getDevelopmentCards()->last();

        // WHEN

        $this->SPLService->reserveCard($player, $card);

        // THEN

        $this->assertNotContains($card, $game->getMainBoard()
            ->getDrawCards()->get($level)
            ->getDevelopmentCards());
        $this->assertSame(1,
            $this->SPLService->getNumberOfTokenAtColor($personal->getTokens(),
                SplendorParameters::$LABEL_JOKER));
    }

    public function testReserveCardFromMainBoardWhenIsAccessibleFromRowWithToken() : void
    {
        // GIVEN
        $game = $this->createGame(SplendorParameters::$MIN_NUMBER_OF_PLAYER);
        $player = $game->getPlayers()->first();
        $personal = $player->getPersonalBoard();

        $level = SplendorParameters::$DEVELOPMENT_CARD_LEVEL_ONE;
        $row = $game->getMainBoard()->getRowsSPL()->get($level);
        $card = $row->getDevelopmentCards()->first();

        // WHEN

        $this->SPLService->reserveCard($player, $card);

        // THEN
        $this->assertNotContains($card, $game->getMainBoard()->getRowsSPL()->get($level)->getDevelopmentCards());
        $this->assertSame(1,
            $this->SPLService->getNumberOfTokenAtColor($personal->getTokens(),
                SplendorParameters::$LABEL_JOKER));
    }

    public function testReserveCardFromMainBoardWhenIsAccessibleFromRowWithoutToken() : void
    {

        // GIVEN
        $game = $this->createGame(SplendorParameters::$MIN_NUMBER_OF_PLAYER);
        $player = $game->getPlayers()->first();
        $personal = $player->getPersonalBoard();

        while ($personal->getTokens()->count() !=
            SplendorParameters::$PLAYER_MAX_TOKEN)
        {
            $token = new TokenSPL();
            $token->setColor("red");
            $player->getPersonalBoard()->addToken($token);
        }

        $level = SplendorParameters::$DEVELOPMENT_CARD_LEVEL_ONE;
        $row = $game->getMainBoard()->getRowsSPL()->get($level);
        $card = $row->getDevelopmentCards()->first();

        // WHEN

        $this->SPLService->reserveCard($player, $card);

        // THEN

        $this->assertNotContains($card, $game->getMainBoard()->getRowsSPL()->get($level)->getDevelopmentCards());
        $this->assertSame(0,
            $this->SPLService->getNumberOfTokenAtColor($personal->getTokens(),
                SplendorParameters::$LABEL_JOKER));
    }

    public function testReserveCardIsNotAccessibleAndTokensIsFull() : void
    {
        // GIVEN
        $game = $this->createGame(SplendorParameters::$MIN_NUMBER_OF_PLAYER);
        $player = $game->getPlayers()->first();
        $personal = $player->getPersonalBoard();

        while ($personal->getTokens()->count() !=
            SplendorParameters::$PLAYER_MAX_TOKEN)
        {
            $token = new TokenSPL();
            $token->setColor("red");
            $player->getPersonalBoard()->addToken($token);
        }

        $card = new DevelopmentCardsSPL();
        $card->setLevel(SplendorParameters::$DEVELOPMENT_CARD_LEVEL_ONE);

        // WHEN et THEN

        $this->expectException(\Exception::class);
        $this->SPLService->reserveCard($player, $card);
    }

    public function testGetDrawCardsByLevelReturnSameCollection() : void
    {
        //GIVEN
        $numberOfPlayers = 2;
        $game = $this->createGame($numberOfPlayers);
        $drawLevelOne = $game->getMainBoard()->getDrawCards()->get(SplendorParameters::$DRAW_CARD_LEVEL_ONE);
        $card = new DevelopmentCardsSPL();
        $card->setLevel(SplendorParameters::$DEVELOPMENT_CARD_LEVEL_ONE);
        $drawLevelOne->addDevelopmentCard($card);
        //WHEN
        $result = $this->SPLService->getDrawCardsByLevel(SplendorParameters::$DRAW_CARD_LEVEL_ONE, $game);
        //THEN
        $this->assertSame($card->getId(), $result->first()->getId());
    }

    public function testGetPurchasableCardsOnBoardWithEnoughMoneyForACard() : void
    {
        //GIVEN
        $numberOfPlayers = 2;
        $game = $this->createGame($numberOfPlayers);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_RED);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $game->getMainBoard()->getRowsSPL()->first()->addDevelopmentCard($developmentCard);

        //WHEN
        $result = $this->SPLService->getPurchasableCardsOnBoard($game, $player);

        //THEN
        $this->assertContains($developmentCard, $result);

    }

    public function testGetPurchasableCardsOnBoardWithNotEnoughMoneyForACard() : void
    {
        //GIVEN
        $numberOfPlayers = 2;
        $game = $this->createGame($numberOfPlayers);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_BLUE);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $game->getMainBoard()->getRowsSPL()->first()->addDevelopmentCard($developmentCard);

        //WHEN
        $result = $this->SPLService->getPurchasableCardsOnBoard($game, $player);

        //THEN
        $this->assertNotContains($developmentCard, $result);

    }

    public function testGetPurchasableCardsOnBoardWithNotEnoughMoneyForACardAndEnoughForAnother() : void
    {
        //GIVEN
        $numberOfPlayers = 2;
        $game = $this->createGame($numberOfPlayers);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_BLUE);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $cardCost2 = new CardCostSPL();
        $cardCost2->setColor(SplendorParameters::$COLOR_BLUE);
        $cardCost2->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard1 = DevelopmentCardsSPL::createDevelopmentCard($array);
        $array2 = new ArrayCollection();
        $array2->add($cardCost2);
        $developmentCard2 = DevelopmentCardsSPL::createDevelopmentCard($array2);
        $game->getMainBoard()->getRowsSPL()->first()->addDevelopmentCard($developmentCard1);
        $game->getMainBoard()->getRowsSPL()->first()->addDevelopmentCard($developmentCard2);

        //WHEN
        $result = $this->SPLService->getPurchasableCardsOnBoard($game, $player);

        //THEN
        $this->assertNotContains($developmentCard1, $result);
        $this->assertContains($developmentCard2, $result);
    }

    public function testGetPurchasableCardsOnPersonalBoardWithEnoughMoneyForACard() : void
    {
        //GIVEN
        $numberOfPlayers = 2;
        $game = $this->createGame($numberOfPlayers);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_RED);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $mock = $this->createPartialMock(SPLService::class, ['getReservedCards']);
        $playerCard = new PlayerCardSPL($player, $developmentCard, true);
        $reservedCard = [$playerCard];
        $mock->method('getReservedCards')->willReturn($reservedCard);

        //WHEN
        $result = $mock->getPurchasableCardsOnPersonalBoard($player);

        //THEN
        $this->assertContains($developmentCard, $result);

    }

    public function testGetPurchasableCardsOnPersonalBoardWithNotEnoughMoneyForACard() : void
    {
        //GIVEN
        $numberOfPlayers = 2;
        $game = $this->createGame($numberOfPlayers);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_BLUE);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $mock = $this->createPartialMock(SPLService::class, ['getReservedCards']);
        $playerCard = new PlayerCardSPL($player, $developmentCard, true);
        $reservedCard = [$playerCard];
        $mock->method('getReservedCards')->willReturn($reservedCard);

        //WHEN
        $result = $mock->getPurchasableCardsOnPersonalBoard($player);

        //THEN
        $this->assertNotContains($developmentCard, $result);

    }

    public function testGetPurchasableCardsOnPersonalBoardWithNotEnoughMoneyForACardAndEnoughForAnother() : void
    {
        //GIVEN
        $numberOfPlayers = 2;
        $game = $this->createGame($numberOfPlayers);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(SplendorParameters::$COLOR_BLUE);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(SplendorParameters::$COLOR_RED);
        $cardCost->setPrice(1);
        $cardCost2 = new CardCostSPL();
        $cardCost2->setColor(SplendorParameters::$COLOR_BLUE);
        $cardCost2->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard1 = DevelopmentCardsSPL::createDevelopmentCard($array);
        $array2 = new ArrayCollection();
        $array2->add($cardCost2);
        $developmentCard2 = DevelopmentCardsSPL::createDevelopmentCard($array2);
        $mock = $this->createPartialMock(SPLService::class, ['getReservedCards']);
        $playerCard1 = new PlayerCardSPL($player, $developmentCard1, true);
        $playerCard2 = new PlayerCardSPL($player, $developmentCard2, true);

        $reservedCard = [$playerCard1, $playerCard2];
        $mock->method('getReservedCards')->willReturn($reservedCard);

        //WHEN
        $result = $mock->getPurchasableCardsOnPersonalBoard($player);

        //THEN
        $this->assertNotContains($developmentCard1, $result);
        $this->assertContains($developmentCard2, $result);
    }

    private function createPlayerCard(PlayerSPL $player, string $color) : PlayerCardSPL
    {
        $card = new DevelopmentCardsSPL();
        $card->setColor($color);
        $card->setPoints(0);
        $card->setLevel(0);
        $playerCard = new PlayerCardSPL($player, $card, false);
        return $playerCard;
    }

    private function createNobleTile(array $param) : NobleTileSPL
    {
        $nobleTile = $this->createPartialMock(NobleTileSPL::class, [
            'getId', 'getCardsCost', 'getPrestigePoints'
        ]);
        $cardsCost = new ArrayCollection();
        foreach ($param as $color => $price) {
            $cardCost = new CardCostSPL();
            $cardCost->setColor($color);
            $cardCost->setPrice($price);
            $cardsCost->add($cardCost);
        }
        $nobleTile->method('getCardsCost')->willReturn($cardsCost);
        $nobleTile->method('getPrestigePoints')->willReturn(SplendorParameters::$POINT_PRESTIGE_BY_NOBLE_TILE);
        $nobleTile->method('getId')->willReturn(0);
        return $nobleTile;
    }

    private function createGame(int $numberOfPlayers) : GameSPL
    {
        $game = new GameSPL();
        for ($i = 0; $i < $numberOfPlayers; ++$i) {
            $player = new PlayerSPL('test', $game);
            $game->addPlayer($player);
            $player->setGameSPL($game);
            $personalBoard = new PersonalBoardSPL();
            $player->setPersonalBoard($personalBoard);
        }
        $mainBoard = new MainBoardSPL();

        // insert discards and rows

        for ($i = 0; $i <= SplendorParameters::$DRAW_CARD_LEVEL_THREE; $i++) {
            $discard = new DrawCardsSPL();
            $discard->setLevel($i);
            for ($c = 0; $c < 10; $c++) {
                $card = new DevelopmentCardsSPL();
                $card->setLevel($i + 1);
                $discard->addDevelopmentCard($card);
            }
            $mainBoard->addDrawCard($discard);
        }

        for ($i = 0; $i <= SplendorParameters::$DRAW_CARD_LEVEL_THREE; $i++) {
            $row = new RowSPL();
            $row->setLevel($i);
            for ($c = 0; $c < 4; $c++) {
                $card = new DevelopmentCardsSPL();
                $card->setLevel($i + 1);
                $row->addDevelopmentCard($card);
            }
            $mainBoard->addRowsSPL($row);
        }

        $joker = new TokenSPL();
        $joker->setColor(SplendorParameters::$LABEL_JOKER);
        $mainBoard->addToken($joker);

        $game->setMainBoard($mainBoard);
        return $game;
    }
}