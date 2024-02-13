<?php

namespace App\Tests\Game\Splendor\Unit\Service;

use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Service\Game\Splendor\SPLService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use App\Repository\Game\Splendor\PlayerSPLRepository;

class SPLServiceTest extends TestCase
{
    private SPLService $SPLService;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $playerRepository = $this->createMock(PlayerSPLRepository::class);
        $this->SPLService = new SPLService($entityManager, $playerRepository);
    }
    public function testTakeTokenWhenAlreadyFull() : void
    {
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $personalBoard->setPlayerSPL($player);
        for ($i = 0; $i < 10; ++$i) {
            $personalBoard->addToken(new TokenSPL());
        }
        $this->assertSame(10, $personalBoard->getTokens()->count());
        $token = new TokenSPL();
        $this->expectException(\Exception::class);
        $this->SPLService->takeToken($player, $token);
    }

    public function testTakeThreeIdenticalTokens() : void
    {
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $personalBoard->setPlayerSPL($player);
        $token1 = new TokenSPL();
        $token1->setColor("blue");
        $token2 = new TokenSPL();
        $token2->setColor("blue");
        $token3 = new TokenSPL();
        $token3->setColor("blue");
        $personalBoard->addSelectedToken($token1);
        $personalBoard->addSelectedToken($token2);
        $this->expectException(\Exception::class);
        $this->SPLService->takeToken($player, $token3);
    }

    public function testTakeThreeTokensButWithTwiceSameColor() : void
    {
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $personalBoard->setPlayerSPL($player);
        $token1 = new TokenSPL();
        $token1->setColor("blue");
        $token2 = new TokenSPL();
        $token2->setColor("red");
        $token3 = new TokenSPL();
        $token3->setColor("blue");
        $personalBoard->addSelectedToken($token1);
        $personalBoard->addSelectedToken($token2);
        $this->expectException(\Exception::class);
        $this->SPLService->takeToken($player, $token3);
    }

    public function testTakeFourTokens() : void
    {
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $personalBoard->setPlayerSPL($player);
        $token1 = new TokenSPL();
        $token1->setColor("blue");
        $token2 = new TokenSPL();
        $token2->setColor("red");
        $token3 = new TokenSPL();
        $token3->setColor("green");
        $token4 = new TokenSPL();
        $token4->setColor("yellow");
        $personalBoard->addSelectedToken($token1);
        $personalBoard->addSelectedToken($token2);
        $personalBoard->addSelectedToken($token3);
        $this->expectException(\Exception::class);
        $this->SPLService->takeToken($player, $token4);
    }

    public function testIsGameEndedShouldReturnFalseBecauseNotLastPlayer() : void
    {
        //GIVEN
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $game->addPlayer($player);
        $personalBoard->setPlayerSPL($player);
        $player2 = new PlayerSPL('test1', $game);
        $personalBoard2 = new PersonalBoardSPL();
        $player2->setPersonalBoard($personalBoard2);
        $personalBoard2->setPlayerSPL($player2);
        $game->addPlayer($player2);
        $player->setTurnOfPlayer(true);
        //WHEN
        $result = $this->SPLService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }
    public function testIsGameEndedShouldReturnFalseBecauseNotReachedLimit() : void
    {
        //GIVEN
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $player->setTurnOfPlayer(false);
        $game->addPlayer($player);
        $personalBoard->setPlayerSPL($player);
        $player2 = new PlayerSPL('test1', $game);
        $personalBoard2 = new PersonalBoardSPL();
        $player2->setPersonalBoard($personalBoard2);
        $personalBoard2->setPlayerSPL($player2);
        $game->addPlayer($player2);
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
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $game->addPlayer($player);
        $personalBoard->setPlayerSPL($player);
        $player2 = new PlayerSPL('test1', $game);
        $personalBoard2 = new PersonalBoardSPL();
        $player2->setPersonalBoard($personalBoard2);
        $personalBoard2->setPlayerSPL($player2);
        $game->addPlayer($player2);
        $player2->setTurnOfPlayer(true);
        $nobleTile = new NobleTileSPL();
        $nobleTile->setPrestigePoints(SPLService::$MAX_PRESTIGE_POINTS);
        $player2->getPersonalBoard()->addNobleTile($nobleTile);
        //WHEN
        $result = $this->SPLService->isGameEnded($game);
        //THEN
        $this->assertTrue($result);
    }

    public function testIsGameEndedShouldReturnFalseBecauseReachedLimitButNotLastPlayer() : void
    {
        //GIVEN
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $game->addPlayer($player);
        $personalBoard->setPlayerSPL($player);
        $player2 = new PlayerSPL('test1', $game);
        $personalBoard2 = new PersonalBoardSPL();
        $player2->setPersonalBoard($personalBoard2);
        $personalBoard2->setPlayerSPL($player2);
        $game->addPlayer($player2);
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
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setPersonalBoard($personalBoard);
        $player->setTurnOfPlayer(true);
        $game->addPlayer($player);
        $personalBoard->setPlayerSPL($player);
        $player2 = new PlayerSPL('test1', $game);
        $personalBoard2 = new PersonalBoardSPL();
        $player2->setPersonalBoard($personalBoard2);
        $player2->setTurnOfPlayer(false);
        $personalBoard2->setPlayerSPL($player2);
        $game->addPlayer($player2);
        $nobleTile1 = new NobleTileSPL();
        $nobleTile1->setPrestigePoints(2);
        $player->getPersonalBoard()->addNobleTile($nobleTile1);
        $nobleTile2 = new NobleTileSPL();
        $nobleTile2->setPrestigePoints(3);
        $player2->getPersonalBoard()->addNobleTile($nobleTile2);
        $expectedRanking = array($player2, $player);
        // WHEN
        $result = $this->SPLService->getRanking($game);
        // THEN
        $this->assertEquals($expectedRanking, $result);
    }
}