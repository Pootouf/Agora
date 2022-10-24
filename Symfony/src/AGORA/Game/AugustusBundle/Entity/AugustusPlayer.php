<?php

namespace AGORA\Game\AugustusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AugustusPlayer
 *
 * @ORM\Table(name="augustus_player")
 * @ORM\Entity(repositoryClass="AGORA\Game\AugustusBundle\Repository\AugustusPlayerRepository")
 */
class AugustusPlayer
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
     * @var int
     *
     * @ORM\Column(name="advantage", type="integer")
     */
    private $advantage;

    /**
     * @var int
     *
     * @ORM\Column(name="legion", type="integer")
     */
    private $legion;

    /**
     * @var int
     *
     * @ORM\Column(name="legion_max", type="integer")
     */
    private $legionMax;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var int
     *
     * @ORM\Column(name="wheat", type="integer")
     */
    private $wheat;

    /**
     * @var int
     *
     * @ORM\Column(name="gold", type="integer")
     */
    private $gold;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_lock", type="boolean")
     */
    private $isLock;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_winner", type="boolean")
     */
    private $isWinner;

    /**
     * @var array
     *
     * @ORM\Column(name="history", type="array")
     */
    private $history;

    /**
     * @var array
     *
     * @ORM\Column(name="equivalences", type="array")
     */
    private $equivalences;

    /**
     * @ORM\OneToMany(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusCard", mappedBy="player", cascade={"persist"})
     */
    private $cards;

    /**
     * @ORM\OneToMany(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusCard", mappedBy="playerCtrl", cascade={"persist"})
     */
    private $ctrlCards;

    /**
     * @ORM\ManyToOne(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusGame", inversedBy="players", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
    */
    private $game;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AGORA\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string")
     */
    private $userName;

    /**
     * Identifie la date du dernier coup joue par le joueur.
     *
     * @var datetime
     *
     * @ORM\Column(name="last_move_played", type="datetime", nullable=true)
     */
    private $lastMovePlayed;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cards = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ctrlCards = new \Doctrine\Common\Collections\ArrayCollection();
        $this->gold = 0;
        $this->wheat = 0;
        $this->legion = 7;
        $this->legionMax = 7;
        $this->advantage = 0;
        $this->score = 0;
        $this->isLock = false;
        $this->isWinner = false;
        $this->equivalences = [];
        $this->equivalences[AugustusToken::SHIELD] = [];
        $this->equivalences[AugustusToken::KNIFE] = [];
        $this->equivalences[AugustusToken::CHARIOT] = [];
        $this->equivalences[AugustusToken::DOUBLESWORD] = [];
        $this->equivalences[AugustusToken::CATAPULT] = [];
        $this->equivalences[AugustusToken::JOKER] = [];
        $this->equivalences[AugustusToken::TEACHES] = [];
    }

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
     * Set score.
     *
     * @param int $score
     *
     * @return AugustusPlayer
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score.
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set legion.
     *
     * @param int $legion
     *
     * @return AugustusPlayer
     */
    public function setLegion($legion)
    {
        $this->legion = $legion;

        return $this;
    }

    /**
     * Get legion.
     *
     * @return int
     */
    public function getLegion()
    {
        return $this->legion;
    }

    /**
     * Set legionMax.
     *
     * @param int $legionMax
     *
     * @return AugustusPlayer
     */
    public function setLegionMax($legionMax)
    {
        $this->legionMax = $legionMax;

        return $this;
    }

    /**
     * Get legionMax.
     *
     * @return int
     */
    public function getLegionMax()
    {
        return $this->legionMax;
    }

    /**
     * Set wheat.
     *
     * @param int $wheat
     *
     * @return AugustusPlayer
     */
    public function setWheat($wheat)
    {
        $this->wheat = $wheat;

        return $this;
    }

    /**
     * Get wheat.
     *
     * @return int
     */
    public function getWheat()
    {
        return $this->wheat;
    }

    /**
     * Set gold.
     *
     * @param int $gold
     *
     * @return AugustusPlayer
     */
    public function setGold($gold)
    {
        $this->gold = $gold;

        return $this;
    }

    /**
     * Get gold.
     *
     * @return int
     */
    public function getGold()
    {
        return $this->gold;
    }

    /**
     * Add card.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusCard $card
     *
     * @return AugustusPlayer
     */
    public function addCard(\AGORA\Game\AugustusBundle\Entity\AugustusCard $card)
    {
        $this->cards[] = $card;

        // Attention on ajoute ici le joueur à la carte il faut donc absolument pas ajouter
        // la carte au joueur dans la fonction setPlayer de Card.
        $card->setPlayer($this);

        return $this;
    }

    /**
     * Remove card.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusCard $card
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCard(\AGORA\Game\AugustusBundle\Entity\AugustusCard $card)
    {   
        $card->setPlayer(null);
        return $this->cards->removeElement($card);
    }

    /**
     * Get cards.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCards()
    {
       $cmp = function ($a, $b) {
            if ($a->getNumber() == $b->getNumber()) {
                return 0;
            }
            return ($a->getNumber() < $b->getNumber()) ? -1 : 1;
        };
        $cards = $this->cards->toArray();
        uasort($cards, $cmp);
        $res = new \Doctrine\Common\Collections\ArrayCollection($cards);
        return $res;
    }

    /**
     * Clear cards.
     *
     * @return array
     */
    public function clearCards()
    {
        $this->cards = array();
        return $this->cards;
    }

    /**
     * Set game.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusGame $game
     *
     * @return AugustusPlayer
     */
    public function setGame(\AGORA\Game\AugustusBundle\Entity\AugustusGame $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game.
     *
     * @return \AGORA\Game\AugustusBundle\Entity\AugustusGame
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set isLock.
     *
     * @param bool $isLock
     *
     * @return AugustusPlayer
     */
    public function setIsLock($isLock)
    {
        $this->isLock = $isLock;
        $this->history = null;
        return $this;
    }

    /**
     * Get isLock.
     *
     * @return bool
     */
    public function getIsLock()
    {
        return $this->isLock;
    }

    /**
     * Set history.
     *
     * @param array $history
     *
     * @return AugustusPlayer
     */
    public function setHistory($history)
    {
        $this->history = $history;

        return $this;
    }

    /**
     * Get history.
     *
     * @return array
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set advantage.
     *
     * @param int $advantage
     *
     * @return AugustusPlayer
     */
    public function setAdvantage($advantage)
    {
        $this->advantage = $advantage;

        return $this;
    }

    /**
     * Get advantage.
     *
     * @return int
     */
    public function getAdvantage()
    {
        return $this->advantage;
    }

    /**
     * Get ctrlCards.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCtrlCards()
    {
        return $this->ctrlCards;
    }

    /**
     * Clear ctrlcards.
     *
     * @return array
     */
    public function clearCtrlCards()
    {
        $this->ctrlCards = array();
        return $this->cards;
    }

    /**
     * Add ctrlCard.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusCard $ctrlCard
     *
     * @return AugustusPlayer
     */
    public function addCtrlCard(\AGORA\Game\AugustusBundle\Entity\AugustusCard $ctrlCard)
    {
        $this->ctrlCards[] = $ctrlCard;
        $ctrlCard->setPlayerCtrl($this);
        return $this;
    }

    /**
     * Remove ctrlCard.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusCard $ctrlCard
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCtrlCard(\AGORA\Game\AugustusBundle\Entity\AugustusCard $ctrlCard)
    {
        return $this->ctrlCards->removeElement($ctrlCard);
    }

    /**
     * Set equivalences.
     *
     * @param array $equivalences
     *
     * @return AugustusPlayer
     */
    public function setEquivalences($equivalences)
    {
        $this->equivalences = $equivalences;

        return $this;
    }

    /**
     * Get equivalences.
     *
     * @return array
     */
    public function getEquivalences()
    {
        return $this->equivalences;
    }

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return AugustusPlayer
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userName.
     *
     * @param string $userName
     *
     * @return AugustusPlayer
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set isWinner.
     *
     * @param bool $isWinner
     *
     * @return AugustusPlayer
     */
    public function setIsWinner($isWinner)
    {
        $this->isWinner = $isWinner;

        return $this;
    }

    /**
     * Get isWinner.
     *
     * @return bool
     */
    public function getIsWinner()
    {
        return $this->isWinner;
    }

    /**
     * Get lastMovePlayed.
     *
     * @return \DateTime|null
     */
    public function getLastMovePlayed()
    {
        return $this->lastMovePlayed;
    }

    /**
     * Set lastMovePlayed.
     *
     * @param \DateTime|null $lastMovePlayed
     *
     * @return AugustusPlayer
     */
    public function setLastMovePlayed($lastMovePlayed = null)
    {
        $this->lastMovePlayed = $lastMovePlayed;

        return $this;
    }
}
