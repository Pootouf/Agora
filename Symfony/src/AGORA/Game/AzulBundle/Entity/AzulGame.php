<?php

namespace AGORA\Game\AzulBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Classe représentant une action possible dans une partie d'Azul
 * 
 * @ORM\Table(name="azul_game")
 * @ORM\Entity(repositoryClass="AGORA\Game\AzulBundle\Repository\AzulGameRepository")
 */
class AzulGame{

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
     * Tableau contenant l'état des fabrics, chaque fabrics est représenté par un tableau associatif suivant ce modèle :
     * "fabric" => "couleur1","couleur2","couleur3","couleur4"
     * 
     * @ORM\Column(name="fabrics", type="array") 
     */
    private array $fabrics;

    /**
     * @var array
     * Tableau associatif représentant le centre de table. Le tableau respecte la forme:
     * "couleur" => nb;
     * 
     * @ORM\Column(name="center", type="array")
     */
    private array $center;

    /**
     * @var array
     * Tableau associatif représentant le sachet de tuile de la forme:
     * "couleur" => nb;
     * 
     * @ORM\Column(name="reserve", type="array")
     */
    private array $reserve;

    /**
     * @var array
     * Tableau associatif représentant les tuiles écarte du jeu de la forme:
     * "couleur" => nb;
     * 
     * @ORM\Column(name="out_tile", type="array")
     */
    private array $outTile;

    /**
     * @var int
     * id du joueur dont c'est le tour.
     * 
     * @ORM\Column(name="current_player_id", type="integer")
     */
    private int $currentPlayerId;

    /**
     * @var array
     * tableau des id des joueurs participant a la partie, ordonné par ordre de jeu
     * 
     * @ORM\Column(name="player_ordered",type="array")
     */
    private array $playerOrdered;

    /**
     * Constructeur d'une action. 
     */
    public function __construct(){
        $this->reserve = array("red" => 20, "blue" => 20, "orange" => 20, "black" => 20, "cyan" => 20);
        $this->currentPlayerId = -1;
        $this->playerOrdered = array();
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
     * Get tableau associatif représentant les tuiles écarte du jeu de la forme:
     */ 
    public function getOutTile()
    {
        return $this->outTile;
    }

    /**
     * Set tableau associatif représentant les tuiles écarte du jeu de la forme:
     *
     * @return  self
     */ 
    public function setOutTile($outTile)
    {
        $this->outTile = $outTile;

        return $this;
    }

    /**
     * Get tableau associatif représentant le sachet de tuile de la forme:
     */ 
    public function getReserve()
    {
        return $this->reserve;
    }

    /**
     * Set tableau associatif représentant le sachet de tuile de la forme:
     *
     * @return  self
     */ 
    public function setReserve($reserve)
    {
        $this->reserve = $reserve;

        return $this;
    }

    /**
     * Get tableau associatif représentant le centre de table. Le tableau respecte la forme:
     */ 
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Set tableau associatif représentant le centre de table. Le tableau respecte la forme:
     *
     * @return  self
     */ 
    public function setCenter($center)
    {
        $this->center = $center;

        return $this;
    }

    /**
     * Get tableau contenant l'état des fabrics, chaque fabrics est représenté par un tableau associatif suivant ce modèle :
     * @return array $fabrics
     */ 
    public function getFabrics()
    {
        return $this->fabrics;
    }

    /**
     * Set tableau contenant l'état des fabrics, chaque fabrics est représenté par un tableau associatif suivant ce modèle :
     *
     * @return  self
     */ 
    public function setFabrics($fabrics)
    {
        $this->fabrics = $fabrics;

        return $this;
    }

    /**
     * Get id du joueur dont c'est le tour.
     *
     * @return  int
     */ 
    public function getCurrentPlayerId()
    {
        return $this->currentPlayerId;
    }

    /**
     * Set id du joueur dont c'est le tour.
     *
     * @param  int  $currentPlayerId  id du joueur dont c'est le tour.
     *
     * @return  self
     */ 
    public function setCurrentPlayerId(int $currentPlayerId)
    {
        $this->currentPlayerId = $currentPlayerId;

        return $this;
    }

    /**
     * Get tableau des id des joueurs participant a la partie, ordonné par ordre de jeu
     *
     * @return  array
     */ 
    public function getPlayerOrdered()
    {
        return $this->playerOrdered;
    }

    /**
     * Set tableau des id des joueurs participant a la partie, ordonné par ordre de jeu
     *
     * @param  array  $playerOrdered  tableau des id des joueurs participant a la partie, ordonné par ordre de jeu
     *
     * @return  self
     */ 
    public function setPlayerOrdered(array $playerOrdered)
    {
        $this->playerOrdered = $playerOrdered;

        return $this;
    }

    /**
     * Méthode permettant d'ajouter un joueur à la partie.
     * 
     * @param int $playerId 
     */
    public function addPlayer($playerId){
        array_push($this->playerOrdered, $playerId);
    }

}

?>