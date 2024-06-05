<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use PHPUnit\Framework\TestCase;

class WarehouseLineGLMTest extends TestCase
{

    private WarehouseLineGLM $warehouseLineGLM;

    public function testInit() : void
    {
        // GIVEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->warehouseLineGLM->getId() >= 0);
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceGLM();

        // GIVEN

        $this->warehouseLineGLM->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->warehouseLineGLM->getResource());
    }

    public function testSetQuantity() : void
    {
        // GIVEN

        $quantity = 100;

        // WHEN

        $this->warehouseLineGLM->setQuantity($quantity);

        // THEN

        $this->assertSame($quantity, $this->warehouseLineGLM->getQuantity());
    }

    public function testSetCoinNumber() : void
    {
        // GIVEN

        $coin = 5;

        // WHEN

        $this->warehouseLineGLM->setCoinNumber($coin);

        // THEN

        $this->assertSame($coin, $this->warehouseLineGLM->getCoinNumber());
    }

    public function testSetWarehouse() : void
    {
        // GIVEN

        $warehouse = new WarehouseGLM();

        // WHEN

        $this->warehouseLineGLM->setWarehouseGLM($warehouse);

        // THEN

        $this->assertEquals($warehouse, $this->warehouseLineGLM->getWarehouseGLM());
    }

    protected function setUp(): void
    {
        $this->warehouseLineGLM = new WarehouseLineGLM();
    }

}