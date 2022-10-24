<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 01/05/18
 * Time: 15:51
 */

namespace AGORA\Game\AveCesarBundle\Tests\Service;



use AGORA\Game\AveCesarBundle\Service\AveCesarService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AveCesarServiceTest extends KernelTestCase {

    /**
     * @var AveCesarService
     */
    private $service;

    /**
     * @var EntityManager
     */
    private $doctrine;

    protected function setUp() {
        self::bootKernel();
        $this->service = static::$kernel
            ->getContainer()
            ->get('agora_game.ave_cesar');
        $this->doctrine = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testCreateRoom() {
        // GIVEN
        $rep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        // WHEN
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        // THEN
        $game = $rep->find($gameId);
        $this->assertEquals("test", $game->getGameName());
        $this->assertEquals(3, $game->getPlayersNb());
        $this->assertEquals(false, $game->getPrivate());
        $this->assertEquals("waiting", $game->getState());
    }

    public function testCreatePlayer() {
        // GIVEN
        $playersRep = $this->doctrine->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer');
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $usersRep = $this->doctrine->getRepository('AGORAUserBundle:User');
        $user = $usersRep->find(1);
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        // WHEN
        $playerId = $this->service->createPlayer($user, $avcGameId);
        // THEN
        $player = $playersRep->find($playerId);
        $this->assertEquals($gameId, $player->getGameId());
        $this->assertEquals("", $player->getHand());
        $this->assertEquals("0b", $player->getPosition());
        $this->assertEquals(1, $player->getLap());
        $this->assertEquals($user, $player->getUserId());
        $this->assertEquals(false, $player->getCesar());
    }

    public function testGetPlayerFromUserId() {
        // GIVEN
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $usersRep = $this->doctrine->getRepository('AGORAUserBundle:User');
        $user = $usersRep->find(1);
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        $playerId = $this->service->createPlayer($user, $avcGameId);
        // WHEN
        $player = $this->service->getPlayerFromUserId($avcGameId, 1);
        // THEN
        $this->assertNotNull($player);
    }

    public function testGetPlayer() {
        // GIVEN
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $usersRep = $this->doctrine->getRepository('AGORAUserBundle:User');
        $user = $usersRep->find(1);
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        $playerId = $this->service->createPlayer($user, $avcGameId);
        // WHEN
        $player = $this->service->getPlayer($avcGameId, $playerId);
        // THEN
        $this->assertNotNull($player);
    }

    public function testGetPlayerName() {
        // GIVEN
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $usersRep = $this->doctrine->getRepository('AGORAUserBundle:User');
        $user = $usersRep->find(1);
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        $playerId = $this->service->createPlayer($user, $avcGameId);
        // WHEN
        $name = $this->service->getPlayerName($playerId);
        // THEN
        $this->assertEquals($user->getUsername(), $name);
    }

    public function testPlayerAlreadyCreated() {
        // GIVEN
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $usersRep = $this->doctrine->getRepository('AGORAUserBundle:User');
        $user = $usersRep->find(1);
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        $playerId = $this->service->createPlayer($user, $avcGameId);
        // WHEN
        $created = $this->service->playerAlreadyCreated($avcGameId, $user->getId());
        // THEN
        $this->assertTrue($created);
    }

    public function testGetMaxPlayer() {
        // GIVEN
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        // WHEN
        $maxPlayers = $this->service->getMaxPlayer($gameId);
        // THEN
        $this->assertEquals(3, $maxPlayers);
    }

    public function testGetGame() {
        // GIVEN
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        // WHEN
        $game = $this->service->getGame($gameId);
        // THEN
        $this->assertNotNull($game);
    }

    public function testGetGameName() {
        // GIVEN
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        // WHEN
        $name = $this->service->getGameName($gameId);
        // THEN
        $this->assertEquals("test", $name);
    }

    public function testInitPlayers() {
        // GIVEN
        $playersRep = $this->doctrine->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer');
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $usersRep = $this->doctrine->getRepository('AGORAUserBundle:User');
        $user = $usersRep->find(1);
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        $playerId = $this->service->createPlayer($user, $avcGameId);
        // WHEN
        $this->service->initPlayers($avcGameId);
        // THEN
        $player = $playersRep->find($playerId);
        $this->assertRegExp("/[1-6],[1-6],[1-6]/", $player->getHand());
    }

    public function testMovePlayer() {
        // GIVEN
        $playersRep = $this->doctrine->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer');
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $usersRep = $this->doctrine->getRepository('AGORAUserBundle:User');
        $user = $usersRep->find(1);
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        $playerId = $this->service->createPlayer($user, $avcGameId);
        $this->service->initPlayers($avcGameId);
        $player = $this->service->getPlayer($avcGameId, $playerId);
        $hand = preg_split("/,/", $player->getHand());
        $cardToPlay = $hand[0];
        // WHEN
        $this->service->movePlayer($playerId, "15a", $cardToPlay);
        // THEN
        $this->assertEquals("15a", $player->getPosition());
    }

    public function testGetNextPlayer() {
        // GIVEN
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        $this->service->setNextPlayer($avcGameId, 1);
        // WHEN
        $nextPlayer = $this->service->getNextPlayer($avcGameId);
        // THEN
        $this->assertEquals(1, $nextPlayer);
    }

    public function testSetNextPlayer() {
        // GIVEN
        $gamesRep = $this->doctrine->getRepository('AGORAGameGameBundle:Game');
        $gameId = $this->service->createRoom("test", 3, false, "", 1);
        $avcGameId = $gamesRep->find($gameId)->getGameId();
        // WHEN
        $this->service->setNextPlayer($avcGameId, 1);
        // THEN
        $this->assertEquals(1, $this->service->getNextPlayer($avcGameId));
    }


}

