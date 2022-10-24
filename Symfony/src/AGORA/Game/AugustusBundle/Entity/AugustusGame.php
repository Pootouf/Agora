<?php

namespace AGORA\Game\AugustusBundle\Entity;

use AGORA\Game\AugustusBundle\Entity\AugustusPlayer;
use AGORA\Game\AugustusBundle\Entity\AugustusCard;
use AGORA\Game\AugustusBundle\Entity\AugustusBoard;
use AGORA\Game\AugustusBundle\Entity\AugustusToken;

use Doctrine\ORM\Mapping as ORM;

/**
 * AugustusGame
 *
 * @ORM\Table(name="augustus_game")
 * @ORM\Entity(repositoryClass="AGORA\Game\AugustusBundle\Repository\AugustusGameRepository")
 */
class AugustusGame
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
     * @ORM\OneToMany(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusPlayer", mappedBy="game", cascade={"persist"})
     */
    private $players;

    /**
     * @ORM\OneToOne(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusBoard", inversedBy="game", cascade={"persist"})
     */
    private $board;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string")
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string")
     */
    private $state;

    /**
     * @var int
     *
     * @ORM\Column(name="affected_player", type="integer")
     */
    private $affectedPlayer;

    /**
     * @var array
     *
     * @ORM\Column(name="color_loot", type="array")
     */
    private $colorLoot;

    /**
     * @var array
     *
     * @ORM\Column(name="next_states", type="array")
     */
    private $nextStates;

    /**
     * @var array
     *
     * @ORM\Column(name="next_affecteds", type="array")
    */
   private $nextAffecteds;

   /**
    * @var int
    *
    * @ORM\Column(name="gold_owner", type="integer")
    */
   private $goldOwner;

   /**
    * @var int
    *
    * @ORM\Column(name="wheat_owner", type="integer")
    */
   private $wheatOwner;

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
     * Add player.
     *
     * @param AugustusPlayer $player
     *
     * @return AugustusPlayer
     */
    public function addPlayer(AugustusPlayer $player)
    {
        $this->players[] = $player;
        return $this;
    }

    /**
     * Remove player.
     *
     * @param AugustusPlayer $player
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePlayer(AugustusPlayer $player)
    {
        return $this->players->removeElement($player);
    }

    /**
     * Get players.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
        $this->token = AugustusToken::NOTOKEN;
        $this->state = "waiting";
        $this->affectedPlayer = -1;
        $this->colorLoot = array(AugustusColor::SENATOR => -1, AugustusColor::GREEN => -1, AugustusColor::PINK => -1, AugustusColor::ORANGE => -1, "all" => -1);
        $this->nextStates = [];
        $this->nextAffecteds = [];
        $this->goldOwner = -1;
        $this->wheatOwner = -1;
    }

    /**
     * Set board.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusBoard|null $board
     *
     * @return AugustusGame
     */
    public function setBoard(AugustusBoard $board = null)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board.
     *
     * @return \AGORA\Game\AugustusBundle\Entity\AugustusBoard|null
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return AugustusGame
     */
    public function setToken(string $token = "")
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return AugustusGame
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

    /**
     * Set affectedPlayer.
     *
     * @param int $nextAffected
     *
     * @return AugustusGame
     */
    public function setAffectedPlayer(int $affectedPlayer)
    {
        $this->affectedPlayer = $affectedPlayer;

        return $this;
    }

    /**
     * Get affectedPlayer.
     *
     * @return \AGORA\Game\AugustusBundle\Entity\AugustusPlayer|null
     */
    public function getAffectedPlayer()
    {
        return $this->affectedPlayer;
    }

    /**
     * Add colorLoot.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusPlayer $colorLoot
     *
     * @return AugustusGame
     */
    public function addColorLoot(AugustusPlayer $colorLoot)
    {
        $this->colorLoot[] = $colorLoot;

        return $this;
    }

    /**
     * set colorLoot.
     *
     * @param array
     *
     * @return AugustusGame
     */
    public function setColorLoot($colorLoot)
    {
        $this->colorLoot = $colorLoot;

        return $this;
    }

    /**
     * Remove colorLoot.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusPlayer $colorLoot
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeColorLoot(AugustusPlayer $colorLoot)
    {
        return $this->colorLoot->removeElement($colorLoot);
    }

    /**
     * Get colorLoot.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColorLoot()
    {
        return $this->colorLoot;
    }

    /**
     * Add nextState.
     *
     * @param int $nextState
     *
     * @return AugustusGame
     */
    public function addNextState(int $nextState)
    {
        $this->nextStates[] = $nextState;

        return $this;
    }

    /**
     * Remove nextState.
     *
     * @param int $nextState
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNextState(int $nextState)
    {
        return $this->nextStates->removeElement($nextState);
    }

    /**
     * Get nextStates.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNextStates()
    {
        return $this->nextStates;
    }

    /**
     * Add nextAffected.
     *
     * @param int $nextAffected
     *
     * @return AugustusGame
     */
    public function addNextAffected(int $nextAffected)
    {
        $this->nextAffecteds[] = $nextAffected;

        return $this;
    }

    /**
     * Remove nextAffected.
     *
     * @param int $nextAffected
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNextAffected(int $nextAffected)
    {
        return $this->nextAffecteds->removeElement($nextAffected);
    }

    /**
     * Get nextAffecteds.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNextAffecteds()
    {
        return $this->nextAffecteds;
    }

    /**
     * Set nextStates.
     *
     * @param array $nextStates
     *
     * @return AugustusGame
     */
    public function setNextStates($nextStates)
    {
        $this->nextStates = $nextStates;

        return $this;
    }

    /**
     * Set nextAffecteds.
     *
     * @param array $nextAffecteds
     *
     * @return AugustusGame
     */
    public function setNextAffecteds($nextAffecteds)
    {
        $this->nextAffecteds = $nextAffecteds;

        return $this;
    }

    /**
     * Set goldOwner.
     *
     * @param int $goldOwner
     *
     * @return AugustusGame
     */
    public function setGoldOwner($goldOwner)
    {
        $this->goldOwner = $goldOwner;

        return $this;
    }

    /**
     * Get goldOwner.
     *
     * @return int
     */
    public function getGoldOwner()
    {
        return $this->goldOwner;
    }

    /**
     * Set wheatOwner.
     *
     * @param int $wheatOwner
     *
     * @return AugustusGame
     */
    public function setWheatOwner($wheatOwner)
    {
        $this->wheatOwner = $wheatOwner;

        return $this;
    }

    /**
     * Get wheatOwner.
     *
     * @return int
     */
    public function getWheatOwner()
    {
        return $this->wheatOwner;
    }
}
