<?php

namespace App\Entity\Game\DTO\Glenmore;

use App\Entity\Game\Glenmore\PlayerGLM;

class PlayerResourcesDataGLM
{
    private PlayerGLM $player;
    private array $productionsCount;
    private int $movementPoints;
    private int $whiskyCount;

    public function __construct(PlayerGLM $playerGLM, array $productionsCount, int $movementPoints, int $whiskyCount) {
        $this->player = $playerGLM;
        $this->productionsCount = $productionsCount;
        $this->movementPoints = $movementPoints;
        $this->whiskyCount = $whiskyCount;
    }

    /**
     * getPlayer : return the player who possessed resources data
     * @return PlayerGLM
     */
    public function getPlayer(): PlayerGLM
    {
        return $this->player;
    }

    /**
     * getProductionsCount : return the array containing the count of each resources possessed by the player
     * @return array
     */
    public function getProductionsCount(): array
    {
        return $this->productionsCount;
    }

    /**
     * getMovementPoints : return the integer representing the actual possessed movement points by the player
     * @return int
     */
    public function getMovementPoints(): int
    {
        return $this->movementPoints;
    }

    /**
     * getWhiskyCount : return the integer representing the count of whisky possessed by the player
     * @return int
     */
    public function getWhiskyCount(): int
    {
        return $this->whiskyCount;
    }


}