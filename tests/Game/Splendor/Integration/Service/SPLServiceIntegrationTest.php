<?php


namespace App\Tests\Game\Splendor\Integration\Service;

use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Splendor\SPLService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SPLServiceIntegrationTest extends KernelTestCase
{

    public function testTakeTokenWhenAlreadyFull(): void
    {
        $splendorService = static::getContainer()->get(SPLService::class);
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
        $splendorService->takeToken($player, $token);
    }

    public function testTakeThreeIdenticalTokens(): void
    {
        $splendorService = static::getContainer()->get(SPLService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(4);
        $player = $game->getPlayers()[0];
        $personalBoard = $player->getPersonalBoard();
        for ($i = 0; $i < 2; ++$i) {
            $token = new TokenSPL();
            $token->setType("joyau");
            $token->setColor("blue");
            $selectedToken = new SelectedTokenSPL();
            $selectedToken->setToken($token);
            $entityManager->persist($token);
            $entityManager->persist($selectedToken);
            $personalBoard->addSelectedToken($selectedToken);
        }
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->persist($personalBoard);
        $entityManager->flush();
        $token = new TokenSPL();
        $this->expectException(\Exception::class);
        $splendorService->takeToken($player, $token);
    }

    public function testTakeThreeTokensButWithTwiceSameColor(): void
    {
        $splendorService = static::getContainer()->get(SPLService::class);
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
        $splendorService->takeToken($player, $token);
    }

    public function testTakeFourTokens(): void
    {
        $splendorService = static::getContainer()->get(SPLService::class);
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
        $splendorService->takeToken($player, $token);
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
            array_push($result, $tmp->isTurnOfPlayer());
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
            array_push($result, $tmp->isTurnOfPlayer());
        }
        $this->assertSame($expectedResult, $result);
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
            $player = new PlayerSPL();
            $player->setUsername('test');
            $player->setGameSPL($game);
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
