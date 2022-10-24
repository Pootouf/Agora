<?php

namespace AGORA\Game\RRBundle\Entity;

use AGORA\Game\RRBundle\Model\RailType;
use AGORA\Game\RRBundle\Model\RailwayType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un chemin de fer dans le jeu Russian Railroads formaté pour être sauvegarder dans la base de donnée
 * 
 * @ORM\Table(name="rr_railway")
 * @ORM\Entity(repositoryClass="AGORA\Game\RRBundle\Repository\RRRailwayRepository")
 */
class RRRailway{

    /**
     * Id du chemin de fer permettant de le différencier dans la table rr_railway.
     * 
     * @var int
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * Indique quel chemin de fer est ce chemin de fer.
     * Ce type ne prendre que les valeurs définit dans l'enum.
     * RailwayType
     * 
     * @var int
     * 
     * @ORM\Column(name="railway_Type",type="integer")
     */
    private int $railwayType;

    /**
     * Indique la taille du chemin de fer. Cette taille est
     * défini en fonction de son type de chemin de fer.
     * 
     * @var int
     * 
     * @ORM\Column(name="length",type="integer")
     */
    private int $length;

    /**
     * Un tableau associatif contenant en clé le type de rail et en valeur sa position sur le chemin de fer.
     * 
     * @var array
     * 
     * @ORM\Column(name="rail_slots",type="array")
     */
    private Array $railSlots;

    /**
     * Constructeur d'un chemin de fer.
     * Sa taille dépends de son type, pour un trans-siberian il aura une taille 15, pour un St Petersburg 
     * et Kiev il aura une taille de 9.
     * Dans tout les cas un rail noir sera présent sur la première case du chemin de fer.
     *  
     * @param RailwayType newRailwayType Définit quel type de chemin de fer il s'agit.
     */
    public function __construct(int $newRailwayType)
    {
        $this -> railSlots = Array(RailType::black => 0, RailType::grey => -1,RailType::brown => -1 );
        $this -> railwayType = $newRailwayType;
        switch($newRailwayType)
        {
            case RailwayType::transsiberian:
                $this -> length = 15;
                $this -> railSlots[RailType::beige] = -1;
                $this -> railSlots[RailType::white] = -1;
                break;
            case RailwayType::stPetersburg:
                $this -> length = 9;
                $this -> railSlots[RailType::beige] = -1;
                $this -> railSlots[RailType::white] = -2;
                break;
            case RailwayType::kiev: 
                $this -> length = 9;
                $this -> railSlots[RailType::beige] = -2;
                $this -> railSlots[RailType::white] = -2;
                break;
            default:
                return null;
        }

    }
    
    /**
     * Accesseur au type du chemin de fer.
     * 
     * @return RailwayType Le type du chemin de fer.
     */
    public function getRailwayType(){
        return $this -> railwayType;
    }

    /**
     * Accesseur a la longueur du chemin de fer.
     * 
     * @return int La taille du chemin de fer.
     */
    public function getLength(){
        return $this -> length;
    }

    /**
     * Accesseur aux différentes cases du chemin de fer.
     * 
     * @return Array L'array qui contient toutes cases du chemin de fer.
     */
    public function getRailSlots(){
        $ret = $this -> railSlots;
        return $ret;
    }

    /**
     * Méthode permettant de construire un rail et donc de l'avancer sur la piste du
     * chemin de fer.
     * 
     * @param int pos Entier qui représente la position où construire le rail
     * @param RailType rail Type du rail qu'on souhaite construire. Ne peut prendre que les valeurs définit dans Railtype
     */
    public function setRailSlot(int $pos, int $rail){
        //On vérifie que la position choisi est bien sur le chemin de fer
        if($pos >= 0 && $pos < $this-> length ){
            //On vérifi que l'on avance bien le rail d'une seul case.
            if( $this->railSlots[$rail] != -2 && $pos == $this->railSlots[$rail] +1){
                //Si le rail n'est pas noir il faut vérifier qu'il soit toujours dérrière le rail un valeur inférieur.
                if($rail != RailType::black){
                    if($this->railSlots[$rail-1] > $pos){
                        $this ->railSlots[$rail] = $pos;
                    }
                } else {
                    $this-> railSlots[$rail] = $pos;
                }
            }
        }
    }
}

?>