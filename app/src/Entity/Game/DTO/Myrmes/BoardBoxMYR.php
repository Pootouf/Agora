<?php

namespace App\Entity\Game\DTO\Myrmes;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\TileMYR;
use Exception;

/**
 * A representation of board's box for the main board of Myrmes.
 * A box can contain an ant, a pheromone tile and a tile
 */
class BoardBoxMYR
{
    private ?TileMYR $tile;
    private ?GardenWorkerMYR $ant;
    private ?PheromonTileMYR $pheromonTile;
    private ?AnthillHoleMYR $anthillHole;
    private ?PreyMYR $prey;
    private int $coordX;
    private int $coordY;

    /**
     * @throws Exception
     */
    public function __construct(?TileMYR $tile, ?GardenWorkerMYR $ant, ?PheromonTileMYR $pheromonTile,
                                ?AnthillHoleMYR $anthillHoleMYR, ?PreyMYR $preyMYR, int $coordX, int $coordY)
    {
        if ($tile == null && ($ant != null || $pheromonTile != null)) {
            throw new Exception("Invalid placement");
        }
        $this->tile = $tile;
        $this->ant = $ant;
        $this->pheromonTile = $pheromonTile;
        $this->coordX = $coordX;
        $this->coordY = $coordY;
        $this->anthillHole = $anthillHoleMYR;
        $this->prey = $preyMYR;
    }

    /**
     * hasTile : indicate if the board's box has a tile
     * @return bool
     */
    public function hasTile() : bool
    {
        return $this->tile != null;
    }

    /**
     * isEmptyBox : indicate if the board's box is empty
     * @return bool
     */
    public function isEmptyBox() : bool
    {
        return $this->ant == null && $this->tile == null && $this->pheromonTile == null;
    }

    /**
     * getTile : return the tile presents on the board's box
     * @return ?TileMYR
     */
    public function getTile() : ?TileMYR
    {
        return $this->tile;
    }

    /**
     * getAnt: return the ant on the tile or null if no ant
     * @return GardenWorkerMYR|null
     */
    public function getAnt(): ?GardenWorkerMYR
    {
        return $this->ant;
    }

    /**
     * getPheromonTile: return the pheromone tile on the tile or null if no pheromone tile
     * @return PheromonTileMYR|null
     */
    public function getPheromonTile(): ?PheromonTileMYR
    {
        return $this->pheromonTile;
    }

    /**
     * getCoordX : return the X position of the box on the main board
     * @return int
     */
    public function getCoordX(): int
    {
        return $this->coordX;
    }

    /**
     * getCoordY : return the Y position of the box on the main board
     * @return int
     */
    public function getCoordY(): int
    {
        return $this->coordY;
    }

    public function getAnthillHole(): ?AnthillHoleMYR
    {
        return $this->anthillHole;
    }

    public function getPrey(): ?PreyMYR
    {
        return $this->prey;
    }
}