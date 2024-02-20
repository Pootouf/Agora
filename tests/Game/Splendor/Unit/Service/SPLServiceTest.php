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
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Repository\Game\Splendor\MainBoardSPLRepository;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use App\Repository\Game\Splendor\TokenSPLRepository;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpCsFixer\Linter\TokenizerLinter;
use PHPUnit\Framework\TestCase;
use App\Repository\Game\Splendor\PlayerSPLRepository;

class SPLServiceTest extends TestCase
{
    private SPLService $SPLService;

    private TokenSPLService $tokenSPLService;
    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $playerRepository = $this->createMock(PlayerSPLRepository::class);
        $mainBoardSPLRepository = $this->createMock(MainBoardSPLRepository::class);
        $tokenRepository = $this->createMock(TokenSPLRepository::class);
        $nobleTileRepository = $this->createMock(NobleTileSPLRepository::class);
        $developmentCardRepository = $this->createMock(DevelopmentCardsSPLRepository::class);
        $this->SPLService = new SPLService($entityManager, $playerRepository, $mainBoardSPLRepository, $tokenRepository,
            $nobleTileRepository, $developmentCardRepository);
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
        $personalBoard->addSelectedToken(new SelectedTokenSPL());
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
        $nobleTile->setPrestigePoints(SPLService::$MAX_PRESTIGE_POINTS - 1);
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
        $nobleTile->setPrestigePoints(SPLService::$MAX_PRESTIGE_POINTS);
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
        $nobleTile->setPrestigePoints(SPLService::$MAX_PRESTIGE_POINTS);
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
        $player->setTurnOfPlayer(false);
        $player2 = $game->getPlayers()->last();
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

    public function testBuyCardWhenNotEnoughMoney()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $playerCard = new PlayerCardSPL($player, new DevelopmentCardsSPL(), false);
        $playerCard->setDevelopmentCard($developmentCard);
        // WHEN
        $this->SPLService->buyCard($player, $playerCard);
        // THEN
        $this->assertNotContains($playerCard ,$player->getPersonalBoard()->getPlayerCards());
    }
    public function testBuyCardWhenEnoughMoney()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $token = new TokenSPL();
        $token->setColor(TokenSPL::$COLOR_RED);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $playerCard = new PlayerCardSPL($player, new DevelopmentCardsSPL(), false);
        $playerCard->setDevelopmentCard($developmentCard);
        // WHEN
        $this->SPLService->buyCard($player, $playerCard);
        // THEN
        $this->assertContains($playerCard ,$player->getPersonalBoard()->getPlayerCards());
    }
    public function testBuyCardWhenCardIsReserved()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(true);
        $token = new TokenSPL();
        $token->setColor(TokenSPL::$COLOR_RED);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(1);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $playerCard = new PlayerCardSPL($player, new DevelopmentCardsSPL(), false);
        $playerCard->setDevelopmentCard($developmentCard);
        $playerCard->setIsReserved(true);
        // WHEN
        $this->SPLService->buyCard($player, $playerCard);
        // THEN
        $this->assertContains($playerCard ,$player->getPersonalBoard()->getPlayerCards());
        $this->assertFalse($playerCard->isIsReserved());
    }

    public function testAddBuyableNobleTilesToPlayerShouldAddTileToPlayer() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        for ($i = 0; $i < 3; $i++) {
            $card = new DevelopmentCardsSPL();
            $card->setColor(TokenSPL::$COLOR_RED);
            $playerCard = new PlayerCardSPL();
            $playerCard->setIsReserved(false);
            $playerCard->setDevelopmentCard($card);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
            $card = new DevelopmentCardsSPL();
            $card->setColor(TokenSPL::$COLOR_BLUE);
            $playerCard = new PlayerCardSPL();
            $playerCard->setIsReserved(false);
            $playerCard->setDevelopmentCard($card);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
        }
        $nobleTile = new NobleTileSPL();
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_BLUE);
        $cardCost->setPrice(3);
        $nobleTile->addCardsCost($cardCost);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(3);
        $nobleTile->addCardsCost($cardCost);
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
            $card = new DevelopmentCardsSPL();
            $card->setColor(TokenSPL::$COLOR_RED);
            $playerCard = new PlayerCardSPL();
            $playerCard->setIsReserved(false);
            $playerCard->setDevelopmentCard($card);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
            $card = new DevelopmentCardsSPL();
            $card->setColor(TokenSPL::$COLOR_BLUE);
            $playerCard = new PlayerCardSPL();
            $playerCard->setIsReserved(false);
            $playerCard->setDevelopmentCard($card);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
        }
        $nobleTile = new NobleTileSPL();
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_BLUE);
        $cardCost->setPrice(3);
        $nobleTile->addCardsCost($cardCost);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(4);
        $nobleTile->addCardsCost($cardCost);
        $game->getMainBoard()->addNobleTile($nobleTile);
        $expectedNumberOfNobleTile = 0;
        //WHEN
        $this->SPLService->addBuyableNobleTilesToPlayer($game, $player);
        //THEN
        $this->assertSame($expectedNumberOfNobleTile, $player->getPersonalBoard()->getNobleTiles()->count());
    }

    private function createGame(int $numberOfPlayers) : GameSPL
    {
        $game = new GameSPL();
        for ($i = 0; $i < $numberOfPlayers; ++$i) {
            $player = new PlayerSPL('test', $game);
            $game->addPlayer($player);
            $personalBoard = new PersonalBoardSPL();
            $player->setPersonalBoard($personalBoard);
        }
        $mainBoard = new MainBoardSPL();

        // insert discards and rows

        for ($i = 0; $i <= DrawCardsSPL::$LEVEL_THREE; $i++) {
            $discard = new DrawCardsSPL();
            $discard->setLevel($i);
            for ($c = 0; $c < 10; $c++) {
                $card = new DevelopmentCardsSPL();
                $card->setLevel($i);
                $discard->addDevelopmentCard($card);
            }
            $mainBoard->addDrawCard($discard);
        }

        for ($i = 0; $i <= DrawCardsSPL::$LEVEL_THREE; $i++) {
            $row = new RowSPL();
            $row->setLevel($i);
            for ($c = 0; $c < 4; $c++) {
                $tcard = new DevelopmentCardsSPL();
                $tcard->setLevel($i);
                $row->addDevelopmentCard($tcard);
            }
            $mainBoard->addRowsSPL($row);
        }

        $game->setMainBoard($mainBoard);
        return $game;
    }

    public function testReserveCardsFromMainBoardWhenIsAccessibleFromDiscard() : void
    {
        // GIVEN
        $game = $this->createGame(SPLService::$MIN_COUNT_PLAYER);
        $player = $game->getPlayers()->first();

        $level = DevelopmentCardsSPL::$LEVEL_ONE;
        $discard = $game->getMainBoard()->getDrawCards()->get($level);
        $card = $discard->getDevelopmentCards()->last();

        // WHEN

        $this->SPLService->reserveCards($player, $card);

        // THEN

        $this->assertContains($card,
        $this->SPLService
            ->getReserveCards($player));
        $this->assertNotContains($card, $game->getMainBoard() ->getDrawCards()->get($level)
            ->getDevelopmentCards());
    }

    public function testReserveCardsFromMainBoardWhenIsNotAccessibleFromDiscard() : void
    {
        // GIVEN
        $game = $this->createGame(SPLService::$MIN_COUNT_PLAYER);
        $player = $game->getPlayers()->first();

        $level = DevelopmentCardsSPL::$LEVEL_ONE - 1;
        $discard = $game->getMainBoard()->getDrawCards()->get($level);
        $card = $discard->getDevelopmentCards()->get(0);

        // WHEN et THEN
        $this->expectException(\Exception::class);
        $this->SPLService->reserveCards($player, $card);
    }

    public function testReserveCardsFromMainBoardWhenIsAccessibleFromRow() : void
    {

        // GIVEN
        $game = $this->createGame(SPLService::$MIN_COUNT_PLAYER);
        $player = $game->getPlayers()->first();

        $level = DevelopmentCardsSPL::$LEVEL_ONE;
        $row = $game->getMainBoard()->getRowsSPL()->get($level);
        $card = $row->getDevelopmentCards()->first();

        // WHEN

        $this->SPLService->reserveCards($player, $card);

        // THEN

        $this->assertTrue($this->SPLService
            ->getReserveCards($player)
            ->contains($card));
        $this->assertFalse($game->getMainBoard()
            ->getRowsSPL()->get($level)
            ->getDevelopmentCards()->contains($card));
    }

    public function testReserveCardsWhenAlreadyFull() : void
    {
        // GIVEN
        $game = $this->createGame(SPLService::$MIN_COUNT_PLAYER);
        $player = $game->getPlayers()->first();
        $level = DevelopmentCardsSPL::$LEVEL_ONE;

        for ($i = 0; $i < SPLService::$MAX_COUNT_RESERVED_CARDS; $i++)
        {
            $row = $game->getMainBoard()->getRowsSPL()->get($level);
            $card = $row->getDevelopmentCards()->first();
            $this->SPLService->reserveCards($player, $card);
        }

        // WHEN et THEN
        $card = $game->getMainBoard()->getDrawCards()
            ->get($level)->getDevelopmentCards()->last();
        $this->expectException(\Exception::class);
        $this->SPLService->reserveCards($player, $card);
    }
}