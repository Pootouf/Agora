<?php

namespace AGORA\Game\SQPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SQPGame
 *
 * @ORM\Table(name="sqp_game")
 * @ORM\Entity(repositoryClass="AGORA\Game\SQPBundle\Repository\GameRepository")
 */
class SQPGame
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
     * @ORM\Column(name="deck", type="string", length=500)
     */
    private $deck;

    /**
     * @var string
     *
     * @ORM\Column(name="board", type="string", length=128)
     */
    private $board;

    /**
     * @var int
     *
     * @ORM\Column(name="turn", type="integer")
     */
    private $turn;

    /**
     * Identifie l'etat de la partie :
     *    -en attente de joueurs = waiting
     *    -en attente de lancement = full
     *    -en cours = ongoing
     *
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

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
     * Set deck.
     *
     * @param string $deck
     *
     * @return SQPGame
     */
    public function setDeck($deck)
    {
        $this->deck = $deck;

        return $this;
    }

    /**
     * Get deck.
     *
     * @return string
     */
    public function getDeck()
    {
        return $this->deck;
    }

    /**
     * Set board.
     *
     * @param string $board
     *
     * @return SQPGame
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
     * Set turn.
     *
     * @param int $turn
     *
     * @return SQPGame
     */
    public function setTurn($turn)
    {
        $this->turn = $turn;

        return $this;
    }

    /**
     * Get turn.
     *
     * @return int
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return SQPGame
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
}
