<?php

namespace AGORA\Game\MorpionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MorpionGame
 *
 * @ORM\Table(name="morpion_game")
 * @ORM\Entity(repositoryClass="AGORA\Game\MorpionBundle\Repository\MorpionGameRepository")
 */
class MorpionGame
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
     * @var string     
     *
     * @ORM\Column(name="board", type="string", length=255)
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
        $this->board = ";;;;;;;;";
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
     * @return MorpionGame
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
     * @return MorpionGame
     */
    public function setCurrentPlayerId(int $nextPlayer)
    {
        $this->currentPlayerId = $nextPlayer;

        return $this;
    }

    /**
     * Get currentPlayerId.
     *
     * @return \AGORA\Game\MorpionBundle\Entity\MorpionPlayer|null
     */
    public function getCurrentPlayerId()
    {
        return $this->currentPlayerId;
    }
}
