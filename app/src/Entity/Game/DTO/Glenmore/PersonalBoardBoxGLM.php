<?php

namespace App\Entity\Game\DTO\Glenmore;

use App\Entity\Game\Glenmore\PlayerTileGLM;

/**
 * Represents the placement of a box on the personal board grid.
 * The x coordinate represents the row, the y coordinate represents the column
 */
class PersonalBoardBoxGLM
{
    private ?PlayerTileGLM $playerTile;

    private int $coordX;

    private int $coordY;

    private bool $placeable;

    public function __construct(?PlayerTileGLM $playerTileGLM, int $coordX, int $coordY, bool $placeable)
    {
        $this->playerTile = $playerTileGLM;
        $this->coordX = $coordX;
        $this->coordY = $coordY;
        $this->placeable = $placeable;
    }

    /**
     * getPlayerTileGLM : return the playerTile of the box
     * @return PlayerTileGLM|null
     */
    public function getPlayerTile(): ?PlayerTileGLM
    {
        return $this->playerTile;
    }

    /**
     * getCoordX : return the coordinate x of the box
     * @return int
     */
    public function getCoordX(): int
    {
        return $this->coordX;
    }

    /**
     * getCoordY : return the coordinate y of the box
     * @return int
     */
    public function getCoordY(): int
    {
        return $this->coordY;
    }

    /**
     * isPlaceable : return if the buying tile is placeable on the personal board
     * @return bool
     */
    public function isPlaceable(): bool
    {
        return $this->placeable;
    }


}
