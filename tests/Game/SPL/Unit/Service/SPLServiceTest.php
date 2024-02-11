<?php


use App\Entity\Game\SPL\GameSPL;
use App\Entity\Game\SPL\PersonalBoardSPL;
use App\Entity\Game\SPL\PlayerSPL;
use App\Entity\Game\SPL\TokenSPL;
use App\Service\Game\SPL\SPLService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PLServiceTest extends TestCase
{
    private SPLService $SPLService;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->SPLService = new SPLService($entityManager);
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
        $personalBoard->addToken($token1);
        $personalBoard->addToken($token2);
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
        $personalBoard->addToken($token1);
        $personalBoard->addToken($token2);
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
        $personalBoard->addToken($token1);
        $personalBoard->addToken($token2);
        $personalBoard->addToken($token3);
        $this->expectException(\Exception::class);
        $this->SPLService->takeToken($player, $token4);
    }


}