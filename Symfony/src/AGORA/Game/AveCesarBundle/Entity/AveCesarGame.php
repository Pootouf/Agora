<?php

namespace AGORA\Game\AveCesarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AveCesarGame
 *
 * @ORM\Table(name="ave_cesar_game")
 * @ORM\Entity(repositoryClass="AGORA\Game\AveCesarBundle\Repository\AveCesarGameRepository")
 */
class AveCesarGame
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
     * @ORM\Column(name="next_player", type="integer")
     */
    private $nextplayer;


    /**
     * @var int
     *
     * @ORM\Column(name="first_player", type="integer")
     */
    private $firstplayer;

    /**
     * @var int
     *
     * @ORM\Column(name="board_id", type="integer")
     */
    private $boardId;

    function __construct(int $boardId) {
        $this->boardId = $boardId;
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
     * Get nextplayer.
     *
     * @return int
     */
    public function getNextplayer()
    {
        return $this->nextplayer;
    }

    /**
     * Set nextplayer.
     *
     * @param int $nextplayer
     *
     * @return AveCesarGame
     */
    public function setNextplayer($nextplayer)
    {
        $this->nextplayer = $nextplayer;

        return $this;
    }

    /**
     * Get firstplayer.
     *
     * @return int
     */
    public function getFirstplayer()
    {
        return $this->firstplayer;
    }

    /**
     * Set firstplayer.
     *
     * @param int $firstplayer
     */
    public function setFirstplayer($firstplayer)
    {
        $this->firstplayer = $firstplayer;
    }

    /**
     * Get the board id
     * 
     * @return int
     */
    public function getBoardId(): int
    {
        return $this->boardId;
    }
}
