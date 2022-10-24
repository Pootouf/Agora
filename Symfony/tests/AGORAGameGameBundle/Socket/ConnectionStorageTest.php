<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 28/05/18
 * Time: 14:26
 */

namespace Tests\AGORAGameGameBundle\Socket;

use AGORA\Game\Socket\ConnectionStorage;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;

class ConnectionStorageTest extends TestCase {

    public function testGetConnection() {
        // GIVEN
        $storage = new ConnectionStorage();
        $conn = $this->createMock(ConnectionInterface::class);
        $storage->addConnection(0, 0, $conn);
        // WHEN
        $res =  $storage->getConnection(0, 0);
        // THEN
        $this->assertEquals($conn, $res);
    }

    public function testGetAllConnections() {
        // GIVEN
        $storage = new ConnectionStorage();
        $conn1 = $this->createMock(ConnectionInterface::class);
        $storage->addConnection(0, 0, $conn1);
        $conn2 = $this->createMock(ConnectionInterface::class);
        $storage->addConnection(0, 1, $conn2);
        // WHEN
        $connections = $storage->getAllConnections(0);
        // THEN
        $this->assertTrue(
            ($connections[0] === $conn1) && ($connections[1] === $conn2)
                || ($connections[0] === $conn2) && ($connections[1] === $conn1)
        );
    }

    public function testAddConnection() {
        // GIVEN
        $storage = new ConnectionStorage();
        $conn = $this->createMock(ConnectionInterface::class);
        // WHEN
        $storage->addConnection(0, 0, $conn);
        // THEN
        $this->assertEquals($conn, $storage->getConnection(0, 0));
    }

    public function testRemoveConnection() {
        // GIVEN
        $storage = new ConnectionStorage();
        $conn = $this->createMock(ConnectionInterface::class);
        $storage->addConnection(0, 0, $conn);
        // WHEN
        $storage->removeConnection($conn);
        // THEN
        $this->assertNull($storage->getConnection(0, 0));
    }
}
