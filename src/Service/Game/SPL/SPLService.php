<?php

namespace App\Service\Game\SPL;

use App\Entity\Game\SPL\PlayerSPL;
use App\Entity\Game\SPL\TokenSPL;
use Doctrine\ORM\EntityManagerInterface;

class SPLService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * takeToken : player takes a token from the mainBoard
     * @param PlayerSPL $playerSPL
     * @param TokenSPL $tokenSPL
     * @return void
     */
    public function takeToken(PlayerSPL $playerSPL, TokenSPL $tokenSPL) : void
    {
        // Method to check player's tokens count

        // Method to check if player can take this token (2 same tokens method)

        // Method to check if player can take this token (3 different tokens method)

        // player can take and action is performed
    }
}