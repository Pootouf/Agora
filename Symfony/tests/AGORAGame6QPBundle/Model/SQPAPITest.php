<?php


namespace AGORA\Game\SQPBundle\Tests\Model;
use AGORA\Game\GameBundle\Entity\Game;
use AGORA\Game\SQPBundle\Model\SQPAPI;
use AGORA\UserBundle\Entity\User;
use AGORA\Game\SQPBundle\Entity\SQPGame;
use AGORA\Game\SQPBundle\Entity\SQPPlayer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SQPAPITest extends KernelTestCase {
    /**
     * @var SQPAPI
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
            ->get('agora_game_sqp.sqpapi');
        $this->doctrine = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    public function testSupressGame() {
        //GIVEN
        $game = $this->newGame();

        $player = $this->newPlayer($game);
        $this->doctrine->persist($game);
        $this->doctrine->flush();
        $this->doctrine->persist($player);
        $this->doctrine->flush();
        $id = $game->getId();
        //WHEN
       $this->service->supressGame($game->getId());
        //THEN
        $game = $this->doctrine->getRepository('AGORAGameSQPBundle:SQPGame')->find($id);
        $players = $this->doctrine->getRepository('AGORAGameSQPBundle:SQPPlayer')->findBy(array('gameId' => $id));
        $this->assertEquals(0, count($players));
        $this->assertEquals(null, $game);

    }

    public function testNbBeef54() {
        //GIVEN
        $card = 54;
        //WHEN
        $nb = $this->service->getNbBeef($card);
        //THEN
        $this->assertEquals(1, $nb);
    }

    public function testNbBeef55() {
        //GIVEN
        $card = 55;
        //WHEN
        $nb = $this->service->getNbBeef($card);
        //THEN
        $this->assertEquals(7, $nb);
    }

    public function testNbBeef11() {
        //GIVEN
        $card = 11;
        //WHEN
        $nb = $this->service->getNbBeef($card);
        //THEN
        $this->assertEquals(5, $nb);
    }

    public function testNbBeef45() {
        //GIVEN
        $card = 45;
        //WHEN
        $nb = $this->service->getNbBeef($card);
        //THEN
        $this->assertEquals(2, $nb);
    }

    public function testNbBeef10() {
        //GIVEN
        $card = 10;
        //WHEN
        $nb = $this->service->getNbBeef($card);
        //THEN
        $this->assertEquals(3, $nb);

    }

    public function testDistribute() {
        //GIVEN
        $game = $this->newGame();
        $player = $this->newPlayer($game);
        $this->doctrine->persist($game);
        $this->doctrine->flush();
        $this->doctrine->persist($player);
        $this->doctrine->flush();
        $deckAsArray = preg_split("/,/",$game->getDeck());

        //On récupère les 10 dernières cartes et on les enlève du deck
        $handAsArray = array_splice($deckAsArray, count($deckAsArray) - 11);
        $hand = $this->service->arrayOfCardToString($handAsArray);
        //WHEN
        $this->service->distribute($game->getId(), $player->getId());

        //THEN
        $this->assertEquals($hand, $player->getHand());

    }

    public function testSetupBoard() {
        //GIVEN
        $game = $this->newGame();
        $player = $this->newPlayer($game);
        $this->doctrine->persist($game);
        $this->doctrine->flush();
        $this->doctrine->persist($player);
        $this->doctrine->flush();
        $deckAsArray = preg_split("/,/",$game->getDeck());
        $colBoard = array_splice($deckAsArray, count($deckAsArray) - 5);
        //WHEN
        $this->service->setupBoard($game->getId());
        //THEN
        $boardString = $game->getBoard();
        $board = preg_split("/;/",$boardString);
        $r0 = preg_split("/,/", $board[0]);
        $r1 = preg_split("/,/", $board[1]);
        $r2 = preg_split("/,/", $board[2]);
        $r3 = preg_split("/,/", $board[3]);
        $this->assertEquals($colBoard[0], $r0[0]);
        $this->assertEquals($colBoard[1], $r1[0]);
        $this->assertEquals($colBoard[2], $r2[0]);
        $this->assertEquals($colBoard[3], $r3[0]);

    }

    public function testAddCardToHand() {
        //GIVEN
        $game = $this->newGame();
        $player = $this->newPlayer($game);
        $this->doctrine->persist($game);
        $this->doctrine->flush();
        $this->doctrine->persist($player);
        $this->doctrine->flush();
        //WHEN
        $this->service->addCardToHand($player->getId(), 54);
        //THEN
        $handAsArray = preg_split("/,/",$player->getHand());
        $this->assertEquals(54, $handAsArray[count($handAsArray) - 1]);


    }

    public function testAddCardToBoard() {
        //à faire
        $this->assertEquals(1,1);
    }

    public function testCheckEndGame() {
        //à faire
        $this->assertEquals(1,1);
    }



    private function newGame() {
        $game = new SQPGame();
        $game->setBoard(";;;");
        $deck = array();
        for ($i = 1; $i <= 104; ++$i) {
            $deck[$i - 1] = $i;
        }
        shuffle($deck);
        $deckToString = "";
        for ($i = 0; $i < 104; ++$i) {
            $deckToString .= intval($deck[$i]).",";
        }
        $game->setDeck($deckToString);
        $game->setTurn(1);
        return $game;
    }
    private function newPlayer($game) {
        $player = new SQPPlayer();
        //initialisation du joueur
        $usersRep = $this->doctrine->getRepository('AGORAUserBundle:User');
        $user = $usersRep->find($game->getHostId());
        $player->setUserId($user);
        $player->setHand(",,,,,,,,,");
        $player->setScore(0);
        $player->setGameId($game);
        $player->setOrderTurn(0);
        return $player;
    }

    private function newUser() {
        $user = new User();
        return $user;
    }


}
