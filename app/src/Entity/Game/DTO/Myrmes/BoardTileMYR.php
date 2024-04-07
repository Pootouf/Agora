<?php

namespace App\Entity\Game\DTO\Myrmes;

use App\Entity\Game\Myrmes\TileMYR;
use Exception;

/**
 * A representation of board's tile for the main board of Myrmes.
 * A tile can be a pivot for pheromone placement or not
 */
class BoardTileMYR
{
    private ?TileMYR $tileMYR;
    private bool $pivot;

    /**
     * @throws Exception
     */
    public function __construct(?TileMYR $tileMYR, bool $pivot)
    {
        if ($tileMYR == null) {
            throw new Exception("tile can't be null");
        }
        $this->tileMYR = $tileMYR;
        $this->pivot = $pivot;
    }

    /**
     * getTile: returns the tile
     * @return TileMYR
     */
    public function getTile() : TileMYR
    {
        return $this->tileMYR;
    }

    /**
     * isPivot: checks if the tile is considered as the pivot or not
     * @return bool
     */
    public function isPivot() : bool
    {
        return $this->pivot;
    }

}