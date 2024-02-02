<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Player;
use App\Repository\Game\PlayerRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class AbstractGameService
{
    /**
     * getPlayerFromUser : return the player associated with a game
     * @param UserInterface|null $user the user
     * @param int $gameId the id of the game
     * @param PlayerRepository $playerRepository the repository associated with the game of the player
     * @return Player|null the player, null if not found
     */
    public function getPlayerFromUser(?UserInterface $user,
        int $gameId,
        PlayerRepository $playerRepository): ?Player
    {
        if ($user == null) {
            return null;
        }

        return $playerRepository->findOneBy(['username' => $user->getUsername(), 'game' => $gameId]);
    }
}