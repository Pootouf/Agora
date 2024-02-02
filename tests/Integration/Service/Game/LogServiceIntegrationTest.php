<?php

namespace Integration\Service\Game;


use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\AbstractGameService;
use App\Service\Game\LogService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\LogRepository;

class LogServiceIntegrationTest extends KernelTestCase {

    public function testSendLog() : void
    {
        $logRepository = static::getContainer()->get(LogRepository::class);
        $logService = static::getContainer()->get(LogService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = new GameSixQP();
        $game->setGameName(AbstractGameService::$SIXQP_LABEL);
        $player = new PlayerSixQP('test', $game);
        $game->addPlayerSixQP($player);
        $entityManager->persist($player);
        $player->setGame($game);
        $entityManager->persist($game);
        $entityManager->flush();
        $message = "test";
        $logService->sendLog($game, $player, $message);
        $expectedGame = $logRepository->findOneBy(['gameId' => $game->getId()]);
        $expectedMessage = $expectedGame->getMessage();
        $this->assertSame($message, $expectedMessage);
    }

}
