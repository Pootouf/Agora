<?php

namespace AGORA\Game\AugustusBundle\Entity;

use \AGORA\Game\AugustusBundle\Entity\AugustusBoard;
use \AGORA\Game\AugustusBundle\Entity\AugustusGame;
use \AGORA\Game\AugustusBundle\Entity\AugustusCard;
use \AGORA\Game\AugustusBundle\Entity\AugustusColor;
use \AGORA\Game\AugustusBundle\Entity\AugustusToken;
use \AGORA\Game\AugustusBundle\Entity\AugustusResource;
use \AGORA\Game\AugustusBundle\Entity\AugustusPower;

use Doctrine\ORM\Mapping as ORM;

/**
 * AugustusCard
 *
 * @ORM\Table(name="augustus_card")
 * @ORM\Entity(repositoryClass="AGORA\Game\AugustusBundle\Repository\AugustusCardRepository")
 */
class AugustusCard
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
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="scoring", type="integer")
     */
    private $points;

    /**
     * @ORM\ManyToOne(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusBoard", inversedBy="deck", cascade={"persist"})
    */
    private $board;

    /**
     * @var array
     *
     * @ORM\Column(name="tokens", type="array")
     */
    private $tokens;

    /**
     * @var string
     *
     * @ORM\Column(name="power", type="string")
     */
    private $power;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_in_line", type="boolean")
     */
    private $isInLine;

    /**
     * @var enum
     *
     * @ORM\Column(name="color", type="string")
     */
    private $color;

    /**
     * @var enum
     *
     * @ORM\Column(name="resource", type="string")
     */
    private $resource;

    /**
     * @var array
     *
     * @ORM\Column(name="tokens_ctrl", type="array")
     */
    private $ctrlTokens;

    /**
     * @ORM\ManyToOne(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusPlayer", inversedBy="cards", cascade={"persist"})
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusPlayer", inversedBy="ctrlCards", cascade={"persist"})
     */
    private $playerCtrl;

    public function __construct(AugustusBoard $boardId, int $number, string $color, string $resource, int $points, string $power, Array $tokens)
    {
        $this->tokens = $tokens;
        
        $this->ctrlTokens = array_fill(0, count($tokens), 0);
        $this->number = $number;
        if ($resource != null) {
            $this->resource = $resource;
        }
        if ($power != null) {
            $this->power = $power;
        }
        $this->points = $points;
        $this->color = $color;
        $this->board = $boardId;
        $this->isInLine = false;

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
     * Set number.
     *
     * @param int $number
     *
     * @return AugustusCard
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set isInLine.
     *
     * @param boolean $isInLine
     *
     * @return AugustusCard
     */
    public function setIsInLine($isInLine)
    {
        $this->isInLine = $isInLine;

        return $this;
    }

    /**
     * Get isInLine.
     *
     * @return boolean
     */
    public function getIsInLine()
    {
        return $this->isInLine;
    }

    /**
     * Set points.
     *
     * @param int $points
     *
     * @return AugustusCard
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points.
     *
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }


    /**
     * Set tokens.
     *
     * @param array $tokens
     *
     * @return AugustusCard
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;

        return $this;
    }

    /**
     * Get tokens.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Set ctrlTokens.
     *
     * @param array $ctrlTokens
     *
     * @return AugustusCard
     */
    public function setCtrlTokens($ctrlTokens)
    {
        $this->ctrlTokens = $ctrlTokens;

        return $this;
    }

    /**
     * Get ctrlTokens.
     *
     * @return array
     */
    public function getCtrlTokens()
    {
        return $this->ctrlTokens;
    }

    /**
     * Set board.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusBoard|null $board
     *
     * @return AugustusCard
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
     * Set power.
     *
     * @param \AugustusPower $power
     *
     * @return AugustusCard
     */
    public function setPower(AugustusPower $power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Get power.
     *
     * @return \AugustusPower
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * Set color.
     *
     * @param \AugustusColor $color
     *
     * @return AugustusCard
     */
    public function setColor(AugustusColor $color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return \AugustusColor
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set resource.
     *
     * @param \AugustusResource $resource
     *
     * @return AugustusCard
     */
    public function setResource(AugustusResource $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource.
     *
     * @return \AugustusResource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set player.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusPlayer|null $player
     *
     * @return AugustusCard
     */
    public function setPlayer(AugustusPlayer $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player.
     *
     * @return \AGORA\Game\AugustusBundle\Entity\AugustusPlayer|null
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set playerCtrl.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusPlayer|null $playerCtrl
     *
     * @return AugustusCard
     */
    public function setPlayerCtrl(\AGORA\Game\AugustusBundle\Entity\AugustusPlayer $playerCtrl = null)
    {
        $this->playerCtrl = $playerCtrl;

        return $this;
    }

    /**
     * Get playerCtrl.
     *
     * @return \AGORA\Game\AugustusBundle\Entity\AugustusPlayer|null
     */
    public function getPlayerCtrl()
    {
        return $this->playerCtrl;
    }

}
