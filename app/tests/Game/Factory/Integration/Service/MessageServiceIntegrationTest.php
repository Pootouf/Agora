<?php


namespace App\Tests\Game\Factory\Integration\Service;

use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageServiceIntegrationTest extends KernelTestCase
{

    private MessageService $messageService;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->messageService = static::getContainer()->get(MessageService::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }
    public function testSendMessageWhenMessageIsWellFormed()
    {
        // GIVEN
        $game = new GameSixQP();
        $game->setGameName(AbstractGameManagerService::$SIXQP_LABEL);
        $player = new PlayerSixQP('test', $game);
        $game->addPlayerSixQP($player);
        $this->entityManager->persist($player);
        $player->setGame($game);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $content = "test";
        $expectedMessage = $content;
        // WHEN
        $this->messageService->sendMessage($player->getId(), $game->getId(), $content, $player->getUsername());
        // THEN
        $this->assertSame($expectedMessage, $this->messageService->receiveMessage($game->getId())[0]->getContent());
    }

    public function testSendMessageWhenContentIsEmpty()
    {
        // GIVEN
        $game = new GameSixQP();
        $expectedMessage = "Bonjour à tous";
        $game->setGameName(AbstractGameManagerService::$SIXQP_LABEL);
        $player = new PlayerSixQP('test', $game);
        $game->addPlayerSixQP($player);
        $this->entityManager->persist($player);
        $player->setGame($game);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $content = "";
        // WHEN
        $result = $this->messageService->sendMessage($player->getId(), $game->getId(), $content, $player->getUsername());
        // THEN
        $this->assertEquals(-1, $result);
    }

    public function testSendMessageWhenAuthorUsernameIsEmpty()
    {
        // GIVEN
        $game = new GameSixQP();
        $game->setGameName(AbstractGameManagerService::$SIXQP_LABEL);
        $player = new PlayerSixQP('test', $game);
        $game->addPlayerSixQP($player);
        $this->entityManager->persist($player);
        $player->setGame($game);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $content = "test";
        $username = "";
        // WHEN
        $result = $this->messageService->sendMessage($player->getId(), $game->getId(), $content, $username);
        // THEN
        $this->assertEquals(-1, $result);
    }
}