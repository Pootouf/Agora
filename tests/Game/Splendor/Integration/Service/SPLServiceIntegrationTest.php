<?php


namespace App\Tests\Game\Splendor\Integration\Service;

use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\SPL\SPLService;
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
            $entityManager->persist($token);
            $personalBoard->addToken($token);
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
        $entityManager->persist($token);
        $personalBoard->addToken($token);
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("red");
        $entityManager->persist($token);
        $personalBoard->addToken($token);
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
        $entityManager->persist($token);
        $personalBoard->addToken($token);
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("red");
        $entityManager->persist($token);
        $personalBoard->addToken($token);
        $token = new TokenSPL();
        $token->setType("joyau");
        $token->setColor("green");
        $entityManager->persist($token);
        $personalBoard->addToken($token);
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


    private function createGame(int $numberOfPlayer): GameSPL
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = new GameSPL();
        $game->setGameName(AbstractGameManagerService::$SPL_LABEL);
        for ($i = 0; $i < $numberOfPlayer; $i++) {
            $player = new PlayerSPL('test', $game);
            $game->addPlayer($player);
            $personalBoard = new PersonalBoardSPL();
            $personalBoard->setGame($game);
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
