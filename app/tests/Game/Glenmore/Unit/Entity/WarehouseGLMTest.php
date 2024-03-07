<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use PHPUnit\Framework\TestCase;

class WarehouseGLMTest extends TestCase
{

    private WarehouseGLM $warehouseGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->warehouseGLM->getId() >= 0);
        $this->assertNotNull($this->warehouseGLM->getWarehouseLine());
    }

    public function testSetMainBoard() : void
    {
        // GIVEN

        $mainBoard = new MainBoardGLM();

        // WHEN

        $this->warehouseGLM->setMainBoardGLM($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->warehouseGLM->getMainBoardGLM());
        $this->assertSame($this->warehouseGLM, $mainBoard->getWarehouse());
    }

    public function testSetWarehouseLineNotYetAdded() : void
    {
        // GIVEN

        $warehouseLine = new WarehouseLineGLM();

        // WHEN

        $this->warehouseGLM->addWarehouseLine($warehouseLine);

        // THEN

        $this->assertContains($warehouseLine, $this->warehouseGLM->getWarehouseLine());
        $this->assertSame($this->warehouseGLM, $warehouseLine->getWarehouseGLM());
    }

    public function testSetWarehouseLineAlreadyAdded() : void
    {
        // GIVEN

        $warehouseLine = new WarehouseLineGLM();
        $this->warehouseGLM->addWarehouseLine($warehouseLine);
        $length = $this->warehouseGLM->getWarehouseLine()->count();

        // WHEN

        $this->warehouseGLM->addWarehouseLine($warehouseLine);

        // THEN

        $this->assertSame($length, $this->warehouseGLM->getWarehouseLine()->count());
    }

    public function testRemoveWarehouseLineNotYetRemoved() : void
    {
        // GIVEN

        $warehouseLine = new WarehouseLineGLM();
        $this->warehouseGLM->addWarehouseLine($warehouseLine);

        // WHEN

        $this->warehouseGLM->removeWarehouseLine($warehouseLine);

        // THEN
        $this->assertNull($warehouseLine->getWarehouseGLM());
        $this->assertNotContains($warehouseLine, $this->warehouseGLM->getWarehouseLine());
    }
    
    public function testRemoveWarehouseLineAlreadyRemoved() : void
    {
        // GIVEN

        $warehouseLine = new WarehouseLineGLM();
        $this->warehouseGLM->addWarehouseLine($warehouseLine);
        $this->warehouseGLM->removeWarehouseLine($warehouseLine);
        $length = $this->warehouseGLM->getWarehouseLine()->count();

        // WHEN

        $this->warehouseGLM->removeWarehouseLine($warehouseLine);

        // THEN

        $this->assertSame($length, $this->warehouseGLM->getWarehouseLine()->count());
    }

    protected function setUp() : void
    {
        $this->warehouseGLM = new WarehouseGLM();
    }

}