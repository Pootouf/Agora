<?php

namespace AGORA\Game\RRBundle\Entity;

use AGORA\Game\RRBundle\Model\ActionType;
use AGORA\Game\RRBundle\Model\RailType;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Classe représentant le plateau commun dans une partie de Russian Railroad
 * 
 * @ORM\Table(name="rr_board")
 * @ORM\Entity(repositoryClass="AGORA\Game\RRBundle\Repository\RRBoardrepository")
 */
class RRBoard
{
    /**
     * 
     * @var int
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * Tableau représentant les conducteur disponible sur le plateau commun. 
     * Le tableau ne contiendra que des entier compris entre 2 inclus et 15 inclus.
     * 
     * @var array
     * 
     * @ORM\Column(name="driverIdDeck", type="simple_array")
     */
    private array $driverIdDeck;

    /**
     * Tableau représentant l'ordre des joueurs pour le tour.
     * 
     * @var array
     * 
     * @ORM\Column(name="playerOrder", type="simple_array")
     */
    private array $playerOrder;

    // /**
    //  * Tableau contenant toute les actions disponibles sur le plateau commun.
    //  * 
    // //  * @var ArrayCollection
    //  * 
    //  * @ORM\OneToMany(targetEntity="RRAction",cascade={"persist"}, 
    //  *                  mappedBy="id", fetch="EAGER")
    //  */
    // private Collection $actions;

    /**
     * Tableau contenant tout les joueurs de à la partie.
     * 
     * @var ArrayCollection
     * 
     * @ORM\Column(name="actions", type="array")
     */
    private ArrayCollection $actions;

    /**
     * Tableau contenant la pile de train disponible
     * 
     * @var ArrayCollection
     * 
     * do the ORM
     */
    //private ArrayCollection $trainFactoryDeck;

    /**
     * Constructeur du plateau commun. Le constructeur va initialiser la liste des conducteurs selon les règles du jeu
     * Les conducteurs de 2 inclus a 7 inclus seront mélangé et placer sur les case 0, 1 et 2.
     * Les conducteurs de 8 inclus a 15 inclus seront mélangé et placer sur les case 3, 4, 5 et 6  
     */
    public function __construct($playersNb)
    {
        $driverA = array(2,3,4 ,5 ,6 ,7);
        $driverB = array(8,9,10,11,12,13,14,15);

        shuffle($driverA);
        shuffle($driverB);

        $this->driverIdDeck = array($driverA[0], $driverA[1], $driverA[2], $driverB[0], $driverB[1], $driverB[2], $driverB[3]);

        $this->playerOrder = array(1, 2, 3, 4);
        shuffle($this->playerOrder);

        $base = array();
        if ($playersNb > 2) {
            array_push($base,
                new RRAction(1, "Construire 2 rails noirs", false, ActionType::railConstruction, 1, false, Array("action" => ActionType::railConstruction, "railType" => RailType::black, "nb" => 2, "repeatable" => false)),
                new RRAction(3, "Construire 2 rails gris", false, ActionType::railConstruction, 2, false, Array("action" => ActionType::railConstruction, "railType" => RailType::black, "nb" => 3, "repeatable" => false)),
                new RRAction(14, "Prendre une locomotive OU une usine", false, ActionType::trainsFactories, 2, false, Array("action" => ActionType::trainsFactories, "repeatable" => false)),
                new RRAction(21, "Prendre deux ouvriers temporaires", false, ActionType::auxiliaries,1,false,Array("action" => ActionType::auxiliaries, "repeatable" => false)),
                new RRAction(18, "Avancer le marqueur d'industre d'une case et construire un rail noir", false, ActionType::industryAndRailConstruction, 2, false, Array("action" => ActionType::industryAndRailConstruction, "repeatable" => false)),
            );
        }

        $base = array_merge($base,array(
            //  Actions de constructions de rails
            new RRAction(2, "Construire 3 rails noirs"  , false, ActionType::railConstruction, 1, false, Array("action" => ActionType::railConstruction, "railType" => RailType::grey, "nb" => 2, "repeatable" => false)),
            new RRAction(4, "Construire 3 rails gris"  , false, ActionType::railConstruction, 2, false, Array("action" => ActionType::railConstruction, "railType" => RailType::grey, "nb" => 3, "repeatable" => false)),
            new RRAction(5, "Construire 1 rail marron" , false, ActionType::railConstruction, 1, false, Array("action" => ActionType::railConstruction, "railType" => RailType::brown, "nb" => 2, "repeatable" => false)),
            new RRAction(6, "Construire 2 rails marrons", false, ActionType::railConstruction, 2, false, Array("action" => ActionType::railConstruction, "railType" => RailType::brown, "nb" => 3, "repeatable" => false)),
            new RRAction(7, "Construire 1 rail beige"  , false, ActionType::railConstruction, 1, false, Array("action" => ActionType::railConstruction, "railType" => RailType::beige, "nb" => 1, "repeatable" => false)),
            new RRAction(8, "Construire 2 rails beiges" , false, ActionType::railConstruction, 2, false, Array("action" => ActionType::railConstruction, "railType" => RailType::beige, "nb" => 2, "repeatable" => false)),
            new RRAction(9, "Construire 1 rail blanc"  , false, ActionType::railConstruction, 1, false, Array("action" => ActionType::railConstruction, "railType" => RailType::white, "nb" => 1, "repeatable" => false)),
            new RRAction(10, "Construire 2 rails blancs" , false, ActionType::railConstruction, 2, false, Array("action" => ActionType::railConstruction, "railType" => RailType::white, "nb" => 2, "repeatable" => false)),
            new RRAction(11, "Déplacer 2 rails de son choix", false, ActionType::railConstruction, 1, true, Array("action" => ActionType::railConstruction, "railType" => RailType::black, "nb" => 2, "repeatable" => false)),
            new RRAction(12, "Construire 1 rail noir ou 1 rail gris", false, ActionType::railConstruction, 1, false, Array("action" => ActionType::railConstruction, "railType" => RailType::black, "nb" => 2, "repeatable" => true)),
            
            new RRAction(13, "Prendre une locomotive OU une usine", false, ActionType::trainsFactories, 1, false, Array("action" => ActionType::trainsFactories, "repeatable" => false)),
            new RRAction(15, "Prendre une locomotive ET une usine", false, ActionType::trainsFactories, 3, false, Array("action" => ActionType::trainsFactories, "repeatable" => false)),

            new RRAction(16, "Avancer le marqueur d'industre d'une case", false, ActionType::industryAndRailConstruction, 1, false, Array("action" => ActionType::industryAndRailConstruction, "repeatable" => false)),
            new RRAction(17, "Avancer le marqueur d'industre de deux cases", false, ActionType::industryAndRailConstruction, 2, false, Array("action" => ActionType::industryAndRailConstruction, "repeatable" => false)),
        
            new RRAction(19, "Prendre un jeton x2", false, ActionType::auxiliaries,1,false,Array("action" => ActionType::auxiliaries, "repeatable" => false)),
            new RRAction(20, "Prendre deux roubles", false, ActionType::auxiliaries,1,false,Array("action" => ActionType::auxiliaries, "repeatable" => false)),
        ));

        usort($base, array('AGORA\Game\RRBundle\Entity\RRAction', 'compare_RRActions'));

        $this->actions = new ArrayCollection($base);
    }
    

    /**
     * Méthode permettant de simuler un achat de conducteur.
     * Le premier conducteur est retirer du plateau commun et son id est renvoyer
     * 
     * @return int l'id du conducteur acheté
     */
    public function buyFirstDriver()
    {
        $tmpID = $this->driverIdDeck[0];
        $this->driverIdDeck[0] = -1;
        return $tmpID;
    }

    /**
     * Accesseur des conducteur disponible sur une case du plateau commun.
     * 
     * @param int pos Position du conducteur a consulter
     * @return int Renvois l'id du conducteur ou -1 si aucun conducteur n'est présent sur la case demander
     */
    public function getDriver(int $pos){
        if($pos < 0 || $pos >= count($this->driverIdDeck)){
            return -1;
        } else {
            return $this->driverIdDeck[$pos];
        } 
    }

    /**
     * Accesseur des conducteurs disponible sur le plateau commun.
     * 
     * @return array Array des identifiants des conducteurs disponibles sur le plateau commun.
     */
    public function getDrivers(){
        return $this->driverIdDeck;
    }

    /**
     * Accesseur de l'ordre des joueurs.
     * 
     * @return array Array des ordres des joueurs.
     */
    public function getOrder(){
        return $this->playerOrder;
    }

    /**
     * Accesseur de toutes les actions disponibles dans le jeux.
     * 
     * @return ArrayCollection Array contenant les actions disponible.
     */
    public function getActions(){
        return $this->actions;
    }

    public function getActionsArray(){
        return $this->actions->toArray();
    }

    /**
     * Accesseur de l'action a une positon précise.
     * 
     * @param int actionPos Position de l'action a rechercher
     * @return Action l'action a rechercher ou null si elle n'a pas été trouvé
     */
    public function getAction(int $actionId){

        foreach($this->actions->toArray() as $action){
            if($action->getId() == $actionId){
                return $action;
            }
        }

        return null;
    }

    /**
     * Accesseur en écriture des conducteurs disponible sur le plateau commun
     * 
     * @param array newDrivers Array des nouveau conducteur.
     */
    public function setDrivers(array $newDrivers){
        $this->driverIdDeck = $newDrivers;
    }

    /**
     * Accesseur en écriture permettant de redéfinir l'ordre de jeu
     * 
     * @param array newOrder Array contenant les identifiants des joueurs ordonnée selon le nouvelle ordre de jeu.
     */
    public function setOrder(array $newOrder){
        $this->playerOrder = $newOrder;
    }

    /**
     * Accesseurs en écriture des actions disponibles sur le plateau commun.
     * 
     * @param ArrayCollection newAction Array avec les nouvelles action.
     */
    public function setAction(ArrayCollection $newActions)
    {
        $this->actions = $newActions;
    }
}
?>