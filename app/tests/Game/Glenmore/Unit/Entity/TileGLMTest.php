<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\CardGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\TileActivationBonusGLM;
use App\Entity\Game\Glenmore\TileActivationCostGLM;
use App\Entity\Game\Glenmore\TileBuyBonusGLM;
use App\Entity\Game\Glenmore\TileBuyCostGLM;
use App\Entity\Game\Glenmore\TileGLM;
use PHPUnit\Framework\TestCase;

class TileGLMTest extends TestCase
{

    private TileGLM $tileGLM;

    public function testInit() {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->tileGLM->getId() >= 0);
        $this->assertNotNull($this->tileGLM->getBuyPrice());
        $this->assertNotNull($this->tileGLM->getActivationPrice());
        $this->assertNotNull($this->tileGLM->getActivationBonus());
        $this->assertNotNull($this->tileGLM->getBuyBonus());
    }

    public function testAddBuyPriceNotYetAdded() : void
    {
        // GIVEN

        $tileBuyCost = new TileBuyCostGLM();

        // WHEN

        $this->tileGLM->addBuyPrice($tileBuyCost);

        // THEN

        $this->assertContains($tileBuyCost, $this->tileGLM->getBuyPrice());
    }

    public function testAddBuyPriceAlreadyAdded() : void
    {
        // GIVEN

        $tileBuyCost = new TileBuyCostGLM();
        $this->tileGLM->addBuyPrice($tileBuyCost);
        $length = $this->tileGLM->getBuyPrice()->count();

        // WHEN

        $this->tileGLM->addBuyPrice($tileBuyCost);

        // THEN

        $this->assertSame($length, $this->tileGLM->getBuyPrice()->count());
    }

    public function testRemoveBuyPrice() : void
    {
        // GIVEN

        $tileBuyCost = new TileBuyCostGLM();
        $this->tileGLM->addBuyPrice($tileBuyCost);

        // WHEN

        $this->tileGLM->removeBuyPrice($tileBuyCost);

        // THEN

        $this->assertNotContains($tileBuyCost, $this->tileGLM->getBuyPrice());
    }

    public function testAddBuyBonusNotYetAdded() : void
    {
        // GIVEN

        $tileBuyBonus = new TileBuyBonusGLM();

        // WHEN

        $this->tileGLM->addBuyBonus($tileBuyBonus);

        // THEN

        $this->assertContains($tileBuyBonus, $this->tileGLM->getBuyBonus());
    }

    public function testAddBuyBonusAlreadyAdded() : void
    {
        // GIVEN

        $tileBuyBonus = new TileBuyBonusGLM();
        $this->tileGLM->addBuyBonus($tileBuyBonus);
        $length = $this->tileGLM->getBuyBonus()->count();

        // WHEN

        $this->tileGLM->addBuyBonus($tileBuyBonus);

        // THEN

        $this->assertSame($length, $this->tileGLM->getBuyBonus()->count());
    }

    public function testRemoveBuyBonus() : void
    {
        // GIVEN

        $tileBuyBonus = new TileBuyBonusGLM();
        $this->tileGLM->addBuyBonus($tileBuyBonus);

        // WHEN

        $this->tileGLM->removeBuyBonus($tileBuyBonus);

        // THEN

        $this->assertNotContains($tileBuyBonus, $this->tileGLM->getBuyBonus());
    }

    public function testAddActivationPriceNotYetAdded() : void
    {
        // GIVEN

        $tileActivationCost = new TileActivationCostGLM();

        // WHEN

        $this->tileGLM->addActivationPrice($tileActivationCost);

        // THEN

        $this->assertContains($tileActivationCost, $this->tileGLM->getActivationPrice());
    }

    public function testAddActivationPriceAlreadyAdded() : void
    {
        // GIVEN

        $tileActivationCost = new TileActivationCostGLM();
        $this->tileGLM->addActivationPrice($tileActivationCost);
        $length = $this->tileGLM->getActivationPrice()->count();

        // WHEN

        $this->tileGLM->addActivationPrice($tileActivationCost);

        // THEN

        $this->assertSame($length, $this->tileGLM->getActivationPrice()->count());
    }

    public function testRemoveActivationPrice() : void
    {
        // GIVEN

        $tileActivationCost = new TileActivationCostGLM();
        $this->tileGLM->addActivationPrice($tileActivationCost);

        // WHEN

        $this->tileGLM->removeActivationPrice($tileActivationCost);

        // THEN

        $this->assertNotContains($tileActivationCost, $this->tileGLM->getActivationPrice());
    }

    public function testAddActivationBonusNotYetAdded() : void
    {
        // GIVEN

        $tileActivationBonus = new TileActivationBonusGLM();

        // WHEN

        $this->tileGLM->addActivationBonus($tileActivationBonus);

        // THEN

        $this->assertContains($tileActivationBonus, $this->tileGLM->getActivationBonus());
    }

    public function testAddActivationBonusAlreadyAdded() : void
    {
        // GIVEN

        $tileActivationBonus = new TileActivationBonusGLM();
        $this->tileGLM->addActivationBonus($tileActivationBonus);
        $length = $this->tileGLM->getActivationBonus()->count();

        // WHEN

        $this->tileGLM->addActivationBonus($tileActivationBonus);

        // THEN

        $this->assertSame($length, $this->tileGLM->getActivationBonus()->count());
    }

    public function testRemoveActivationBonus() : void
    {
        // GIVEN

        $tileActivationBonus = new TileActivationBonusGLM();
        $this->tileGLM->addActivationBonus($tileActivationBonus);

        // WHEN

        $this->tileGLM->removeActivationBonus($tileActivationBonus);

        // THEN

        $this->assertNotContains($tileActivationBonus, $this->tileGLM->getActivationBonus());
    }

    public function testSetType() : void
    {
        // GIVEN

        $type = GlenmoreParameters::$TILE_TYPE_VILLAGE;

        // WHEN

        $this->tileGLM->setType($type);

        // THEN

        $this->assertSame($type, $this->tileGLM->getType());
    }

    public function testSetName() : void
    {
        // GIVEN

        $name = GlenmoreParameters::$TILE_NAME_DISTILLERY;

        // WHEN

        $this->setName($name);

        // THEN

        $this->assertSame($name, $this->getName());
    }

    public function testSetContainingRiver() : void
    {
        // WHEN

        $this->tileGLM->setContainingRiver(true);

        // THEN

        $this->assertTrue($this->tileGLM->isContainingRiver());
    }

    public function testSetContainingRoad() : void
    {
        // WHEN

        $this->tileGLM->setContainingRoad(false);

        // THEN

        $this->assertFalse($this->tileGLM->isContainingRoad());
    }

    public function testSetLevel() : void
    {
        // GIVEN

        $level = GlenmoreParameters::$TILE_LEVEL_THREE;

        // WHEN

        $this->tileGLM->setLevel($level);

        // THEN

        $this->assertSame($level, $this->tileGLM->getLevel());
    }

    public function testSetCard() : void
    {
        // GIVEN

        $card = new CardGLM();

        // WHEN

        $this->tileGLM->setCard($card);

        // THEN

        $this->assertSame($card, $this->tileGLM->getCard());
    }

    protected function setUp(): void
    {
        $this->tileGLM = new TileGLM();
    }
}