<?php


use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\MessageRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function PHPUnit\Framework\assertSame;

class MessageServiceIntegrationTest extends KernelTestCase
{
    public function testSendMessage()
    {
        // GIVEN
        $messageRepository = static::getContainer()->get(MessageRepository::class);
        $messageService = static::getContainer()->get(MessageService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = new GameSixQP();
        $expectedMessage = "Bonjour à tous";
        // WHEN
        $game->setGameName(AbstractGameManagerService::$SIXQP_LABEL);
        $player = new PlayerSixQP('test', $game);
        $game->addPlayerSixQP($player);
        $entityManager->persist($player);
        $player->setGame($game);
        $entityManager->persist($game);
        $entityManager->flush();
        $messageService->sendMessage($player->getId(), $game->getId(), "Bonjour à tous");
        $message = $messageService->receiveMessage($game->getId())[0]->getContent();
        // THEN
        assertSame($expectedMessage, $message);
    }
}