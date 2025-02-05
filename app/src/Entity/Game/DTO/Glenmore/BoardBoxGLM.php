<?php

namespace App\Entity\Game\DTO\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use Exception;

/**
 * A representation of board's box for the main board of Glenmore.
 * A box can contain a tile or a pawn, but can't contain the two at the same time.
 */
class BoardBoxGLM
{
    private ?PawnGLM $pawn = null;
    private ?BoardTileGLM $tile = null;

    /**
     * @throws Exception
     */
    public function __construct(?BoardTileGLM $tile, ?PawnGLM $pawn)
    {
        if($pawn != null && $tile != null) {
            throw new Exception("Wrong parameters number");
        }
        if($pawn != null) {
            $this->pawn = $pawn;
            $this->tile = null;
        } elseif($tile != null) {
            $this->tile = $tile;
            $this->pawn = null;
        }
    }

    /**
     * hasTile : indicate if the board's box has a tile
     * @return bool
     */
    public function hasTile(): bool
    {
        return $this->tile != null;
    }

    /**
     * hasPawn : indicate if the board's box has a pawn
     * @return bool
     */
    public function hasPawn(): bool
    {
        return $this->pawn != null;
    }

    /**
     * isEmptyBox : indicate if the board's box is empty
     * @return bool
     */
    public function isEmptyBox(): bool
    {
        return $this->pawn == null && $this->tile == null;
    }

    /**
     * getTile : return the tile presents on the board's box
     * @return ?BoardTileGLM
     */
    public function getTile(): ?BoardTileGLM
    {
        return $this->tile;
    }

    /**
     * getPawn : return the pawn presents on the board's box
     * @return ?PawnGLM
     */
    public function getPawn(): ?PawnGLM
    {
        return $this->pawn;
    }
}
