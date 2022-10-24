<?php

namespace AGORA\Game\Puissance4Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Puissance4Game
 *
 * @ORM\Table(name="Puissance4_game")
 * @ORM\Entity(repositoryClass="AGORA\Game\Puissance4Bundle\Repository\Puissance4GameRepository")
 */
class Puissance4Game
{
   /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var array     
     *
     * @ORM\Column(name="board", type="json_array", length=1500)
     */
    private $board;

    /**
     * @var int
     *
     * @ORM\Column(name="current_player_id", type="integer")
     */
    private $currentPlayerId;

    /**
     * Constructor
     */
    public function __construct()
    {
        for ($col = 0; $col < 7; $col++) {
            for ($row = 0; $row < 6; $row++) {
                $this->board[$col][$row] = "none";
            }
        }
        $this->currentPlayerId = -1;
    }


    //METHODES


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set board.
     *
     * @param string $board
     *
     * @return Puissance4Game
     */
    public function setBoard($board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board.
     *
     * @return string
     */
    public function getBoard()
    {
        return $this->board;
    }


    /**
     * Set currentPlayerId.
     *
     * @param int $nextPlayer
     *
     * @return Puissance4Game
     */
    public function setCurrentPlayerId(int $nextPlayer)
    {
        $this->currentPlayerId = $nextPlayer;

        return $this;
    }

    /**
     * Get currentPlayerId.
     *
     * @return \AGORA\Game\Puissance4Bundle\Entity\Puissance4Player|null
     */
    public function getCurrentPlayerId()
    {
        return $this->currentPlayerId;
    }
}
