<?php

namespace AGORA\Game\AzulBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cette classe représente un joueur d'une partie d'Azul
 *
 * @ORM\Table(name="azul_player")
 * @ORM\Entity(repositoryClass="AGORA\Game\AzulBundle\Repository\AzulPlayerRepository")
 */
class AzulPlayer{

    /**
     * Id du joueur d'Azul. Cet id est différent de l'id d'utilisateur.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Id du jeu
     * 
     *  @var int
     *
     * @ORM\ManyToOne(targetEntity="AGORA\Game\AzulBundle\Entity\AzulGame", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $gameId;

    /**
     * Id de l'utilisateur
     * 
     * @var int
     * 
     * @ORM\ManyToOne(targetEntity="AGORA\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;


    /**
     * Nombre de point de victoire du joueur
     *
     * @var int
     *
     * @ORM\Column(name="victoryPointsScale", type="integer")
     */
    private int $victoryPointsScale;

    /**
     * Tableau contenant toutes les tuiles ajoutant un malus après avoir récupéré des tuiles dans une factory
     *
     * @var array
     *
     * @ORM\Column(name="malusTiles", type="array")
     */
    private array $malusTiles;

    /**
     * Tableau de boolean définissant quel portion de mur est construite.
     * 
     * @var array
     * 
     * @ORM\Column(name="wall", type="array")
     */
    private array $wall;

    /**
     * Tableau correspondant au motif du plateau perso du joueur
     * Chaque case a la forme : {couleur, nbTuile}
     * Sachant que le nbTuile ne doit pas dépasser : indice de la ligne + 1
     *  
     * @var array
     * 
     * @ORM\Column(name="motif", type="array")
     */
    private array $motif;

    /**
     * Tableau ne devant contenir que 2 valeurs, et dans cet ordre : 
     * la couleur (sous forme de string)
     * le nombre (sous forme d'entier)
     * @var array
     * 
     * @ORM\Column(name="selectedTile", type="array")
     */
    private array $selectedTile;

    /**
     * Permet de savoir si le joueur possède le token de premier joueur
     *
     * @var bool
     *
     * @ORM\Column(name="firstPlayerToken", type="boolean")
     */
    private bool $firstPlayerToken;

    /**
     * Identifie la date du dernier coup joué par le joueur
     * 
     * @var datetime
     * 
     * @ORM\Column(name="last_move_played", type="datetime", nullable=true)
     */
    private $lastMovePlayed;

    public function getUserId(){
        return $this->userId;
    }

    public function setUserId($userId){
        $this->userId = $userId;
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
     * Set gameId.
     *
     * @param int $gameId
     *
     * @return Player
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Get gameId.
     *
     * @return int
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Set VictoryPoint.
     *
     * @param int $victoryPoint
     *
     * @return Player
     */
    public function setVictoryPointsScale($victoryPoint)
    {
        $this->victoryPointsScale = $victoryPoint;

        return $this;
    }

    /**
     * Get victory points.
     *
     * @return int
     */
    public function getVictoryPointsScale()
    {
        return $this->victoryPointsScale;
    }

    /**
     * Set firstPlayerToken.
     *
     * @param bool $firstPlayerToken
     *
     * @return Player
     */
    public function setFirstPlayerToken($firstPlayerToken)
    {
        $this->firstPlayerToken = $firstPlayerToken;

        return $this;
    }

    /**
     * Get firstPlayerToken.
     *
     * @return bool
     */
    public function getFirstPlayerToken()
    {
        return $this->firstPlayerToken;
    }

    /**
     * Set malusTiles.
     *
     * @param array $malusTiles
     *
     * @return Player
     */
    public function setMalusTiles($malusTiles)
    {
        $this->malusTiles = $malusTiles;

        return $this;
    }

    /**
     * Get malusTiles.
     *
     * @return array
     */
    public function getMalusTiles()
    {
        return $this->malusTiles;
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
     * @return Player
     */
    public function setLastMovePlayed($lastMovePlayed = null)
    {
        $this->lastMovePlayed = $lastMovePlayed;

        return $this;
    }

    /**
     * Get tableau de boolean définissant quel portion de mur est construite.
     *
     * @return  array
     */ 
    public function getWall()
    {
        return $this->wall;
    }

    /**
     * Set tableau de boolean définissant quel portion de mur est construite.
     *
     * @param  array  $wall  Tableau de boolean définissant quel portion de mur est construite.
     *
     * @return  self
     */ 
    public function setWall(array $wall)
    {
        $this->wall = $wall;

        return $this;
    }

    /**
     * Get le nombre (sous forme d'entier)
     *
     * @return  array
     */ 
    public function getSelectedTile()
    {
        return $this->selectedTile;
    }

    /**
     * Set le nombre (sous forme d'entier)
     *
     * @param  array  $selectedTIle  le nombre (sous forme d'entier)
     *
     * @return  self
     */ 
    public function setSelectedTile(array $selectedTile)
    {
        $this->selectedTile = $selectedTile;

        return $this;
    }

    /**
     * Get tableau de taille incrémental (la première ligne a une taille de 1, la seconde de 2 etc) correspondant au motif du plateau perso du joueur
     *
     * @return  array
     */ 
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set tableau de taille incrémental (la première ligne a une taille de 1, la seconde de 2 etc) correspondant au motif du plateau perso du joueur
     *
     * @param  array  $motif  Tableau de taille incrémental (la première ligne a une taille de 1, la seconde de 2 etc) correspondant au motif du plateau perso du joueur
     *
     * @return  self
     */ 
    public function setMotif(array $motif)
    {
        $this->motif = $motif;

        return $this;
    }
}

?>
