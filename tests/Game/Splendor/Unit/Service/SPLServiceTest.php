<?php

namespace App\Tests\Game\Splendor\Unit\Service;

use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
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
        $tokenRepository = $this->createMock(TokenSPLRepository::class);
        $nobleTileRepository = $this->createMock(NobleTileSPLRepository::class);
        $developmentCardRepository = $this->createMock(DevelopmentCardsSPLRepository::class);
        $this->SPLService = new SPLService($entityManager, $playerRepository, $tokenRepository,
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
        $game->setMainBoard($mainBoard);
        return $game;
    }

}