<?php


namespace Game\Splendor\Integration\Service;

use App\Entity\Game\Splendor\CardCostSPL;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SPLServiceIntegrationTest extends KernelTestCase
{

    public function testTakeTokenWhenAlreadyFull(): void
    {
        $tokenSplendorService = static::getContainer()->get(TokenSPLService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(4);
        $player = $game->getPlayers()[0];
        $personalBoard = $player->getPersonalBoard();
        for ($i = 0; $i < PersonalBoardSPL::$MAX_TOKEN; ++$i) {
            $token = new TokenSPL();
            $token->setType("joyau");
            $token->setColor("blue");
            $entityManager->persist($token);
            $personalBoard->addToken($token);
        }
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->persist($personalBoard);
        $entityManager->flush();
        $this->assertSame(10, $personalBoard->getTokens()->count());
        $token = new TokenSPL();
        $this->expectException(\Exception::class);
        $tokenSplendorService->takeToken($player, $token);
    }

    public function testTakeThreeTokensButWithTwiceSameColor(): void
    {
        $tokenSplendorService = static::getContainer()->get(TokenSPLService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(4);
        $player = $game->getPlayers()[0];
        $personalBoard = $player->getPersonalBoard();
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("blue");
        $selectedToken = new SelectedTokenSPL();
        $selectedToken->setToken($token);
        $entityManager->persist($token);
        $entityManager->persist($selectedToken);
        $personalBoard->addSelectedToken($selectedToken);
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("red");
        $selectedToken = new SelectedTokenSPL();
        $selectedToken->setToken($token);
        $entityManager->persist($token);
        $entityManager->persist($selectedToken);
        $personalBoard->addSelectedToken($selectedToken);
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->persist($personalBoard);
        $entityManager->flush();
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("blue");
        $this->expectException(\Exception::class);
        $tokenSplendorService->takeToken($player, $token);
    }

    public function testTakeFourTokens(): void
    {
        $tokenSplendorService = static::getContainer()->get(TokenSPLService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(4);
        $player = $game->getPlayers()[0];
        $personalBoard = $player->getPersonalBoard();
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("blue");
        $selectedToken = new SelectedTokenSPL();
        $selectedToken->setToken($token);
        $entityManager->persist($selectedToken);
        $entityManager->persist($token);
        $personalBoard->addSelectedToken($selectedToken);
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("red");
        $selectedToken = new SelectedTokenSPL();
        $selectedToken->setToken($token);
        $entityManager->persist($selectedToken);
        $entityManager->persist($token);
        $personalBoard->addSelectedToken($selectedToken);
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("green");
        $selectedToken = new SelectedTokenSPL();
        $selectedToken->setToken($token);
        $entityManager->persist($selectedToken);
        $entityManager->persist($token);
        $personalBoard->addSelectedToken($selectedToken);
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->persist($personalBoard);
        $entityManager->flush();
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("yellow");
        $this->expectException(\Exception::class);
        $tokenSplendorService->takeToken($player, $token);
    }

    public function testTakeTokensWithTwoSameColorShouldFailBecauseNotAvailable() : void
    {
        //GIVEN
        $tokenSplendorService = static::getContainer()->get(TokenSPLService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor("red");
        $token->setType("ruby");
        $mainBoard = $game->getMainBoard();
        $mainBoard->addToken($token);
        $token1 = new TokenSPL();
        $token1->setColor("red");
        $token1->setType("ruby");
        $mainBoard->addToken($token1);
        $entityManager->persist($token);
        $entityManager->persist($token1);
        $entityManager->flush();
        $tokenSplendorService->takeToken($player, $token);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $tokenSplendorService->takeToken($player, $token1);
    }

    public function testClearSelectedTokens() : void
    {
        //GIVEN
        $tokenSplendorService = static::getContainer()->get(TokenSPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->get(0);
        $personalBoard = $player->getPersonalBoard();
        $personalBoard->addSelectedToken(new SelectedTokenSPL());
        //WHEN
        $tokenSplendorService->clearSelectedTokens($player);
        //THEN
        $this->assertEmpty($player->getPersonalBoard()->getSelectedTokens());
    }
    public function testIsGameEndedShouldReturnFalseBecauseNotLastPlayer() : void
    {
        //GIVEN
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->get(0);
        $player->setTurnOfPlayer(true);
        //WHEN
        $result = $splendorService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }

    public function testIsGameEndedShouldReturnFalseBecauseNotReachedLimit() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->get(1);
        $player->setTurnOfPlayer(true);
        $nobleTile = new NobleTileSPL();
        $nobleTile->setPrestigePoints(SPLService::$MAX_PRESTIGE_POINTS - 1);
        $player->getPersonalBoard()->addNobleTile($nobleTile);
        $entityManager->persist($nobleTile);
        $entityManager->flush();
        //WHEN
        $result = $splendorService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }

    public function testIsGameEndedShouldReturnTrue() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->get(1);
        $player->setTurnOfPlayer(true);
        $nobleTile = new NobleTileSPL();
        $nobleTile->setPrestigePoints(SPLService::$MAX_PRESTIGE_POINTS);
        $player->getPersonalBoard()->addNobleTile($nobleTile);
        $entityManager->persist($nobleTile);
        $entityManager->flush();
        $splendorService->calculatePrestigePoints($player);
        //WHEN
        $result = $splendorService->isGameEnded($game);
        //THEN
        $this->assertTrue($result);
    }

    public function testIsGameEndedShouldReturnFalseBecauseReachedLimitButNotLastPlayer() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->get(0);
        $player->setTurnOfPlayer(true);
        $nobleTile = new NobleTileSPL();
        $nobleTile->setPrestigePoints(SPLService::$MAX_PRESTIGE_POINTS);
        $player->getPersonalBoard()->addNobleTile($nobleTile);
        $entityManager->persist($nobleTile);
        $entityManager->flush();
        //WHEN
        $result = $splendorService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }
    public function testGetRanking(): void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player1 = $game->getPlayers()[0];
        $player2 = $game->getPlayers()[1];
        $nobleTile1 = new NobleTileSPL();
        $nobleTile1->setPrestigePoints(2);
        $nobleTile2 = new NobleTileSPL();
        $nobleTile2->setPrestigePoints(3);
        $entityManager->persist($nobleTile1);
        $entityManager->persist($nobleTile2);
        $player1->getPersonalBoard()->addNobleTile($nobleTile1);
        $player2->getPersonalBoard()->addNobleTile($nobleTile2);
        $entityManager->flush();
        $splendorService->calculatePrestigePoints($player1);
        $splendorService->calculatePrestigePoints($player2);
        $expectedRanking = array($player2, $player1);
        // WHEN
        $result = $splendorService->getRanking($game);
        // THEN
        $this->assertEquals($expectedRanking, $result);
    }

    public function testGetActivePlayer() : void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player2 = $game->getPlayers()[1];
        $player2->setTurnOfPlayer(true);
        $entityManager->persist($player2);
        $entityManager->flush();
        // WHEN
        $result = $splendorService->getActivePlayer($game);
        // THEN
        $this->assertSame($player2, $result);
    }

    public function testEndRoundOfPlayerWhenNotLastPlayer() : void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()[0];
        $player->setTurnOfPlayer(true);
        $entityManager->persist($player);
        $entityManager->flush();
        $expectedResult = [false, true];
        // WHEN
        $splendorService->endRoundOfPlayer($game, $splendorService->getActivePlayer($game));
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
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player2 = $game->getPlayers()[1];
        $player2->setTurnOfPlayer(true);
        $entityManager->persist($player2);
        $entityManager->flush();
        $expectedResult = [true, false];
        // WHEN
        $splendorService->endRoundOfPlayer($game, $splendorService->getActivePlayer($game));
        // THEN
        $result = Array();
        foreach ($game->getPlayers() as $tmp) {
            $result[] = $tmp->isTurnOfPlayer();
        }
        $this->assertSame($expectedResult, $result);
    }

    public function testBuyCardWhenNotEnoughMoney(): void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(1);
        $entityManager->persist($cardCost);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $developmentCard->setPrestigePoints(1);
        $developmentCard->setColor("red");
        $developmentCard->setLevel(DevelopmentCardsSPL::$LEVEL_ONE);
        $developmentCard->setValue(1);
        $entityManager->persist($developmentCard);
        $playerCard = new PlayerCardSPL($player, $developmentCard, false);
        $playerCard->setPersonalBoardSPL($player->getPersonalBoard());
        $entityManager->persist($playerCard);
        $entityManager->flush();
        // WHEN
        $splendorService->buyCard($player, $playerCard);
        // THEN
        $this->assertNotContains($playerCard, $player->getPersonalBoard()->getPlayerCards());
    }

    public function testBuyCardWhenEnoughMoney()
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(TokenSPL::$COLOR_RED);
        $token->setType("ruby");
        $entityManager->persist($token);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(1);
        $entityManager->persist($cardCost);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $developmentCard->setPrestigePoints(1);
        $developmentCard->setColor("red");
        $developmentCard->setLevel(DevelopmentCardsSPL::$LEVEL_ONE);
        $developmentCard->setValue(1);
        $entityManager->persist($developmentCard);
        $playerCard = new PlayerCardSPL($player, $developmentCard, false);
        $playerCard->setPersonalBoardSPL($player->getPersonalBoard());
        $entityManager->persist($playerCard);
        $entityManager->flush();
        // WHEN
        $splendorService->buyCard($player, $playerCard);
        // THEN
        $this->assertContains($playerCard, $player->getPersonalBoard()->getPlayerCards());
    }

    public function testBuyCardWhenCardIsReserved()
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(TokenSPL::$COLOR_RED);
        $token->setType("ruby");
        $entityManager->persist($token);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(1);
        $entityManager->persist($cardCost);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $developmentCard->setPrestigePoints(1);
        $developmentCard->setColor("red");
        $developmentCard->setLevel(DevelopmentCardsSPL::$LEVEL_ONE);
        $developmentCard->setValue(1);
        $entityManager->persist($developmentCard);
        $playerCard = new PlayerCardSPL($player, $developmentCard, true);
        $playerCard->setPersonalBoardSPL($player->getPersonalBoard());
        $entityManager->persist($playerCard);
        $entityManager->flush();
        // WHEN
        $splendorService->buyCard($player, $playerCard);
        // THEN
        $this->assertContains($playerCard, $player->getPersonalBoard()->getPlayerCards());
        $this->assertFalse($playerCard->isIsReserved());
    }

    public function testTokenRetrievedWhenBuy()
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(TokenSPL::$COLOR_RED);
        $token->setType("ruby");
        $entityManager->persist($token);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(1);
        $entityManager->persist($cardCost);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $developmentCard->setPrestigePoints(1);
        $developmentCard->setColor("red");
        $developmentCard->setLevel(DevelopmentCardsSPL::$LEVEL_ONE);
        $developmentCard->setValue(1);
        $entityManager->persist($developmentCard);
        $playerCard = new PlayerCardSPL($player, $developmentCard, true);
        $playerCard->setPersonalBoardSPL($player->getPersonalBoard());
        $entityManager->persist($playerCard);
        $entityManager->flush();
        // WHEN
        $splendorService->buyCard($player, $playerCard);
        // THEN
        $this->assertNotContains($token, $player->getPersonalBoard()->getTokens());
    }

    public function testGoldTokenRetrievedWhenBuy()
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $token = new TokenSPL();
        $token->setColor(TokenSPL::$COLOR_YELLOW);
        $token->setType("ruby");
        $entityManager->persist($token);
        $player->getPersonalBoard()->addToken($token);
        $cardCost = new CardCostSPL();
        $cardCost->setColor(TokenSPL::$COLOR_RED);
        $cardCost->setPrice(1);
        $entityManager->persist($cardCost);
        $array = new ArrayCollection();
        $array->add($cardCost);
        $developmentCard = DevelopmentCardsSPL::createDevelopmentCard($array);
        $developmentCard->setPrestigePoints(1);
        $developmentCard->setColor("red");
        $developmentCard->setLevel(DevelopmentCardsSPL::$LEVEL_ONE);
        $developmentCard->setValue(1);
        $entityManager->persist($developmentCard);
        $playerCard = new PlayerCardSPL($player, $developmentCard, true);
        $playerCard->setPersonalBoardSPL($player->getPersonalBoard());
        $entityManager->persist($playerCard);
        $entityManager->flush();
        // WHEN
        $splendorService->buyCard($player, $playerCard);
        // THEN
        $this->assertNotContains($token, $player->getPersonalBoard()->getTokens());
    }

    public function testAddBuyableNobleTilesToPlayerShouldAddTileToPlayer() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        for ($i = 0; $i < 3; $i++) {
            $playerCard = $this->createPlayerCard($player, TokenSPL::$COLOR_RED);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
            $playerCard = $this->createPlayerCard($player, TokenSPL::$COLOR_BLUE);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
        }
        $nobleTile = $this->createNobleTile([
            TokenSPL::$COLOR_RED => 3,
            TokenSPL::$COLOR_BLUE => 3,
        ]);
        $game->getMainBoard()->addNobleTile($nobleTile);
        $entityManager->persist($nobleTile);
        $entityManager->persist($player->getPersonalBoard());
        $entityManager->persist($game->getMainBoard());
        $entityManager->flush();
        //WHEN
        $splendorService->addBuyableNobleTilesToPlayer($game, $player);
        //THEN
        $this->assertSame($nobleTile, $player->getPersonalBoard()->getNobleTiles()->first());
    }


    public function testAddBuyableNobleTilesToPlayerShouldDoNothing() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $splendorService = static::getContainer()->get(SPLService::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        for ($i = 0; $i < 3; $i++) {
            $playerCard = $this->createPlayerCard($player, TokenSPL::$COLOR_RED);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
            $playerCard = $this->createPlayerCard($player, TokenSPL::$COLOR_BLUE);
            $player->getPersonalBoard()->addPlayerCard($playerCard);
        }
        $nobleTile = $this->createNobleTile([
            TokenSPL::$COLOR_RED => 3,
            TokenSPL::$COLOR_BLUE => 4,
        ]);
        $game->getMainBoard()->addNobleTile($nobleTile);
        $entityManager->persist($nobleTile);
        $entityManager->persist($player->getPersonalBoard());
        $entityManager->persist($game->getMainBoard());
        $entityManager->flush();
        $expectedNumberOfNobleTile = 0;
        //WHEN
        $splendorService->addBuyableNobleTilesToPlayer($game, $player);
        //THEN
        $this->assertSame($expectedNumberOfNobleTile, $player->getPersonalBoard()->getNobleTiles()->count());
    }

    private function createPlayerCard(PlayerSPL $player, string $color) : PlayerCardSPL
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $card = new DevelopmentCardsSPL();
        $card->setColor($color);
        $card->setPrestigePoints(0);
        $card->setLevel(0);
        $entityManager->persist($card);
        $playerCard = new PlayerCardSPL($player, $card, false);
        $entityManager->persist($playerCard);
        return $playerCard;
    }

    private function createNobleTile(array $param) : NobleTileSPL
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $nobleTile = new NobleTileSPL();
        foreach ($param as $color => $price) {
            $cardCost = new CardCostSPL();
            $cardCost->setColor($color);
            $cardCost->setPrice($price);
            $entityManager->persist($cardCost);
            $nobleTile->addCardsCost($cardCost);
        }
        $nobleTile->setPrestigePoints(0);
        return $nobleTile;
    }

    private function createGame(int $numberOfPlayer): GameSPL
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = new GameSPL();
        $game->setGameName(AbstractGameManagerService::$SPL_LABEL);
        $mainBoard = new MainBoardSPL();
        $mainBoard->setGameSPL($game);
        $entityManager->persist($mainBoard);
        for ($i = 0; $i < $numberOfPlayer; $i++) {
            $player = new PlayerSPL('test', $game);
            $game->addPlayer($player);
            $personalBoard = new PersonalBoardSPL();
            $player->setPersonalBoard($personalBoard);
            $personalBoard->setPlayerSPL($player);
            $entityManager->persist($personalBoard);
            $entityManager->persist($player);
        }
        $entityManager->persist($game);
        $entityManager->flush();
        return $game;
    }

}
