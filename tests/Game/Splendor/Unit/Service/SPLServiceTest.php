<?php

namespace App\Tests\Game\Splendor\Unit\Service;

use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Service\Game\Splendor\SPLService;
use Doctrine\Common\Collections\ArrayCollection;
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

    public function testReserveCardsFromMainBoardWhenIsNotAccessible() : void
    {
        // GIVEN

        // WHEN

        // THEN


    }

    public function testReserveCardsFromMainBoardWhenIsAccessibleFromRow() : void
    {

        // GIVEN
        $game = new GameSPL();
        $mainBoard = new MainBoardSPL();
        $personalBoard = new PersonalBoardSPL();
        $player = new PlayerSPL();

        $game->setMainBoard($mainBoard);
        $game->addPlayer($player);
        $player->setGameSPL($game);
        $player->setPersonalBoard($personalBoard);
        $personalBoard->setPlayerSPL($player);

        $testCard = new DevelopmentCardsSPL();
        $testCard->setLevel(2);

        for ($i = 0; $i < 3; $i++) {
            $row = new RowSPL();
            $row->setLevel($i);
            for ($c = 0; $c < 4; $c++) {
                if ($c == 2 && $i == 2) {
                    break;
                }
                $card = new DevelopmentCardsSPL();
                $card->setLevel($i);
                $row->addDevelopmentCard($card);
            }
            if ($i == 2) {
                $row->addDevelopmentCard($testCard);
                break;
            }
        }

        // WHEN

        $this->SPLService->reserveCards($player, $testCard);

        // THEN

        $this->assertTrue($this->SPLService
            ->getReserveCards($player)
            ->contains($testCard));
    }

    public function testReserveCardsAtPlayer() : void
    {

    }

    public function testReserveCardsAtOtherPlayer() : void
    {

    }

    public function testReserveCardsWhenAlreadyFull() : void
    {

    }

    public function testReserveCardsWhenNotAlreadyFull() : void
    {

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

    public function testEndRoundOfPlayerWhenNotLastPlayer() : void
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
        $expectedResult = [false, true];
        // WHEN
        $this->SPLService->endRoundOfPlayer($game, $player);
        // THEN
        $result = Array();
        foreach ($game->getPlayers() as $tmp) {
            array_push($result, $tmp->isTurnOfPlayer());
        }
        $this->assertSame($expectedResult, $result);
    }

    public function testEndRoundOfPlayerWhenLastPlayer() : void
    {
        // GIVEN
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
        $player2->setTurnOfPlayer(true);
        $personalBoard2->setPlayerSPL($player2);
        $game->addPlayer($player2);
        $expectedResult = [true, false];
        // WHEN
        $this->SPLService->endRoundOfPlayer($game, $player2);
        // THEN
        $result = Array();
        foreach ($game->getPlayers() as $tmp) {
            array_push($result, $tmp->isTurnOfPlayer());
        }
        $this->assertSame($expectedResult, $result);
    }
}