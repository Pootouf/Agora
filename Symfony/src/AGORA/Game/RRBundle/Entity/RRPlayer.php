<?php

namespace AGORA\Game\RRBundle\Entity;

use AGORA\Game\RRBundle\Model\RailType;
use AGORA\Game\RRBundle\Model\RailwayType;
use AGORA\Game\RRBundle\Model\ActionType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cette classe représente un joueur d'une partie de Russian Railroads
 * 
 * @ORM\Table(name="rr_player")
 * @ORM\Entity(repositoryClass="AGORA\Game\RRBundle\Repository\RRPlayerRepository")
 */
class RRPlayer{

    /**
     * Id du joueur de Russian railroad. Cet id est différent de l'id d'utilisateur.
     * 
     * @var int
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="AGORA\Game\RRBundle\Entity\RRGame", cascade={"persist"})
     * @ORM\JoinColumn(name="gameId", referencedColumnName="id")
     */
    private $gameId;


    /**
     * Id de l'utilisateur
     * 
     * @var int
     * 
     * @ORM\ManyToOne(targetEntity="AGORA\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
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
     * Nombre d'ouvrier dont le joueur dispose. Ce nombre est réinitialisé au début de chaque
     * manche
     * 
     * @var int
     * 
     * @ORM\Column(name="workers", type="integer")
     */
    private int $workers;

    /**
     * Couleur qui a été attribué au joueur. 
     * 
     * @var int
     * 
     * @ORM\Column(name="color", type="integer")
     */
    private int $color;

    /**
     * Nombre de rouble que le joueur possède.
     * 
     * @var int
     * 
     * @ORM\Column(name="rubles", type="integer")
     */
    private int $rubles;

    /**
     * Représente l'avvancé industrielle du joueur;
     * 
     * @var int
     * 
     * @ORM\Column(name="industry", type="integer")
     * NOT USED YET
     */
    private int $industry;

    /**
     * Ensemble des id des cartes bonus que le joueur possède
     * tableau de int de taille 0..8
     * 
     * @var array
     * 
     * @ORM\Column(name="bonusCardIds", type="array")
     * NOT USED YET
     */
    private array $bonusCardIds;

    /**
     * Liste des jeton bonus actif. ce tableau de taille 7 initialise toute ses valeurs
     * a false. Lorsque le joueur active un bonus, la case dans le tableau qui lui correspond est mise a vrai
     * correspondance des cases:
     * [0] = construction de rail x4
     * [1] = industrialisation x5
     * [2] = 3 bonus x2
     * [3] = réévaluation
     * [4] = 2 jeton industrialisation
     * [5] = médaille de kiev
     * [6] = Super double bonus! (carte bonus + carte avantage)
     * 
     * @var array
     * 
     * @ORM\Column(name="activeBonusToken", type="array")
     * NOT USED YET
     */
    private array $activeBonusToken;

    /**
     * Liste des cases bonus x2 activé par le joueur.
     * tableau de boolean de taille 8
     * 
     * @var array
     * 
     * @ORM\Column(name="bonusSlots",type="array")
     * NOT USED YET
     */
    private array $bonusSlots;

    /**
     * Liste des conducteur acquis par le joueur.
     * table de int de taille 0..*
     * 
     * @var array
     * 
     * @ORM\Column(name="driverIdDeck",type="array")
     * NOT USED YET
     */
    private array $driverIdDeck;

    /**
     * L'ensemble des trois chemin de fer dont le joueur dispose.
     * Il possède un chemin de fer de chaque type avec l'initialisation par défaut.
     * 
     * @var ArrayCollection
     * 
     * 
     * @ORM\Column(name="railways", type="array")
     */
    private ArrayCollection $railways;

    /**
     * Représente l'ensemble des trains dont le joueur dispose 
     * 
     * @ORM\ManytoMany(targerEntity="TrainFactory")
     * @ORM\JoinTable(name="rr_player_trains", joinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="trains_id", referencedColumnName="id",unique=true)})
     * @ORM\OrderBy({"pos"})
     * private ArrayCollection $train
     */

    /**
     * do the same with factories
    */

    /**
     * Plateau commun de la partie.
     * 
     * @var RRBoard
     * 
     * @ORM\OneToOne(targetEntity="RRBoard")
     * @ORM\JoinColumn(name="board", referencedColumnName="id")
     */
    private RRBoard $board;

    
    /**
     * Cet structure regroupe, pour chaque type de chemin de fer, les cases possédant un bonus et si ils ont été activé au moins une fois.
     * Cette structure n'aura que les boolean de mutable et seront a false à leurs creation.
     * 
     * @var array
     * 
     * @ORM\Column(name="activatedRailwayBonus", type="array")
     */
    private array $activatedRailwayBonus;

    /**
     * Date de la dernière action enregistré d'un joueur.
     * 
     * @var DateTime
     * 
     * @ORM\Column(name="last_move_played",type="datetime")
     */
    private DateTime $lastmove;

    /**
     * Boolean définissant si le joueur a passé son tour
     * 
     * @var bool
     * 
     * @ORM\Column(name="passed", type="boolean")
     */
    private bool $passed;

    /**
     * Constructeur d'un joueur du jeu Russian Railroads
     * 
     * @param int newColor la couleur qui sera attribué au joueur.
     */
    public function __construct(int $newColor)
    {
        $this->id = $newColor; 
        
        $this->color = $newColor;
        $this->victoryPointsScale = 0;
        $this->workers = 5;
        $this->rubles = 1;
        $this->industry = 0;
        $this->bonusCardIds = array();
        $this->activeBonusToken = array(false, false, false, false, false, false ,false );
        $this->bonusSlots = array(false, false, false, false, false, false, false, false);
        $this->driverIdDeck = array();
        $this->railways = new ArrayCollection(array(RailwayType::transsiberian => new RRRailway(RailwayType::transsiberian), RailwayType::stPetersburg => new RRRailway(RailwayType::stPetersburg), RailwayType::kiev => new RRRailway(RailwayType::kiev)));
        
        $this->activatedRailwayBonus = array( RailwayType::stPetersburg => array(3 => false, 5 => false, 6 => false, 8 => false),
                                         RailwayType::transsiberian => array(1 => false, 2 => false, 5 => false, 9 => false, 12 => false, 14 => false),
                                         RailwayType::kiev => array(0 => false, 1 => false, 2 => false, 3 => false, 4 => false, 6 => false, 7 => false, 8 => false ) );
        // ne pas oublier les autres attributs
        $this->passed = false;
        $this->lastmove = new DateTime();
    }

    public function gainPoint(int $pts){
        $this->victoryPointsScale += $pts;
    }

    /**
     * 
     * accesseuer pour doctrine
     */
    public function setWorker(int $newWorker)
    {
        $this->workers = $newWorker;
    }

    public function asPassed(){
        return $this->passed;
    }

    public function setPassed(bool $newPassed)
    {
        $this->passed = $newPassed;
    }

    public function getRubles(){
        return $this->rubles;
    }

    public function setRubles(int $ruble){
        $this->rubles = $ruble;
    }

    public function setUserId($uid) {
        $this->userId = $uid;
    }

    /**
     * Set gameId.
     *
     * @param int $gameId
     *
     * @return RRPlayer
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }


    /**
     * Accesseur de la dernière action effectué par le joueur
     * 
     * @return DateTime la date de la dernière action.
     */
    public function getLastMove()
    {
        return $this->lastmove;
    }

    /**
     * Accesseur des chemin de fer du joueur
     * 
     * @return array une array contenent les RRRailway que le joueur possède
     */
    public function getRailWays()
    {
        return $this->railways;
    }

    public function getId()
    {
        return $this->id;
    }


    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Accesseur des ouvriers que le joueur peut utiliser
     * 
     * @return int Le nombre de travailleur restant
     */
    public function getWorker()
    {
        return $this->workers;
    }

    public function getactivatedRailwayBonus(){
        return $this->activatedRailwayBonus;
    }

    public function addTrain(/*TrainFactory $train, int $index*/)
    {
        //not implemented yet
    }

    public function removeTrain(int $index)
    {
        //not implemented yet
    }

    public function addFactory(/*TrainFactory $factory, int $index */ )
    {
        //not implemented yet
    }

    public function useDriver(int $driverId)
    {
        //not implemented yet
    }

    public function useBonusToken(int $tokenId)
    {
        //not implemented yet
    }

    public function activateBonusCards()
    {
        //not implemented yet
    }

    /**
     * Fonction permettant au joueur de gagner des points en fin de manche.
     * Les points sont donnée en fonction de la longueur et d ela qualité des chemins de fer, des trains possédé et de son niveau d'industrialisation.
     */
    public function calculatePoints()
    {
        $pts = 0;
        $tmpRailway = $this->railways[RailwayType::transsiberian]->getRailSlot();
        for($s = 0; $s < $this->railways[RailwayType::transsiberian]->getLength(); $s++)
        {
            if($s <= $tmpRailway[RailType::white]){
                
                if($this->activeBonusToken[3]){
                    $pts = 10;
                } else {
                    $pts = 7;
                }

                if($s < 8 && $this->bonusSlots[$s]){
                    $pts = $pts *2;
                }
                $this->victoryPointsScale += $pts;

            } else if($s <= $tmpRailway[RailType::beige]){
                if($this->activeBonusToken[3]){
                    $pts = 6;
                } else {
                    $pts = 4;
                }

                if($s < 8 && $this->bonusSlots[$s]){
                    $pts = $pts *2;
                }
                $this->victoryPointsScale += $pts;
            } else if($s <= $tmpRailway[RailType::brown]){
                if($this->activeBonusToken[3]){
                    $pts = 3;
                } else {
                    $pts = 2;
                }

                if($s < 8 && $this->bonusSlots[$s]){
                    $pts = $pts *2;
                }
                $this->victoryPointsScale += $pts;
            } else if($s <= $tmpRailway[RailType::grey]){
                $pts = 1;

                if($s < 8 && $this->bonusSlots[$s]){
                    $pts = $pts *2;
                }
                $this->victoryPointsScale += $pts;
            }
        }

        $pts = 0;
        $tmpRailway = $this->railways[RailwayType::stPetersburg]->getRailSlot();
        for($s = 0; $s < $this->railways[RailwayType::stPetersburg]->getLength(); $s++)
        {
            if($s <= $tmpRailway[RailType::beige]){
                $pts = 4;
                if($this->activatedRailwayBonus[RailwayType::stPetersburg][6])
                {
                    $pts = $pts*2;
                    
                }
                $this->victoryPointsScale += $pts;
            } else if($s <= $tmpRailway[RailType::brown])
            {
                $pts = 2;
                if($this->activatedRailwayBonus[RailwayType::stPetersburg][6])
                {
                    $pts = $pts*2;
                    
                }
                $this->victoryPointsScale += $pts;
            } else if($s <= $tmpRailway[RailType::grey])
            {
                $pts = 1;
                if($this->activatedRailwayBonus[RailwayType::stPetersburg][6])
                {
                    $pts = $pts*2;
                    
                }
                $this->victoryPointsScale += $pts;
            }
        }

        $tmpRailway = $this->railways[RailwayType::kiev]->getRailSlot();
        for($s = 0; $s < $this->railways[RailwayType::kiev]->getLength();$s++)
        {
            if($s <= $tmpRailway[RailType::brown]){
                $this->victoryPointsScale += 2;
            } else if($s <= $tmpRailway[RailType::grey])
            {
                $this->victoryPointsScale += 1;
            }
        }

        if($this->activatedRailwayBonus[RailwayType::kiev][7])
        {
            $this->victoryPointsScale += 10;
        }
        if($this->activatedRailwayBonus[RailwayType::kiev][3])
        {
            $this->victoryPointsScale += 4;
        }
        if($this->activatedRailwayBonus[RailwayType::kiev][2])
        {
            $this->victoryPointsScale += 3;
        }
        if($this->activatedRailwayBonus[RailwayType::kiev][1])
        {
            $this->victoryPointsScale += 2;
        }
        if($this->activatedRailwayBonus[RailwayType::kiev][0])
        {
            $this->victoryPointsScale += 1;
        }

        switch($this->industry){
            case 15:
                $this->victoryPointsScale += 30;
                break;
            case 14:
                $this->victoryPointsScale += 25;
                break;
            case 13 :
            case 12 :
                $this->victoryPointsScale += 30;
                break;
            case 11:
            case 10:
            case 9 :
            case 8 :
                $this->victoryPointsScale += 15;
                break;
            case 7:
            case 6:
                $this->victoryPointsScale += 10;
                break;
            case 5:
            case 4:
                $this->victoryPointsScale += 5;
                break;
            case 3:
                $this->victoryPointsScale += 3;
                break;
            case 2:
                $this->victoryPointsScale += 2;
                break;
            case 1: 
                $this->victoryPointsScale += 1;
                break;
        }
        //to do: the second industry

        $this->workers = 5;
        if($this->activatedRailwayBonus[RailwayType::transsiberian][2])
        {
            $this->workers ++;
        }

        if($this->activatedRailwayBonus[RailwayType::kiev][6])
        {
            $this->workers ++;
        }

    }

    /**
     * Cette fonction permet au joueur de jouer une action. L'action est désigné par
     * son id
     * 
     * Le details de l'action varie en fonction de l'action effectué:
     * Pour une construction de rail il contiendra le chemin de fer [railway] où il est construit, le type de rail construit [rail] et la position [pos]
     * Pour un achat de train ou d'usine il contiendra l'id du train et/ou de l'usine et leurs position ou ils sont construit
     * Pour de l'industrialisation il ne contiendra le nombre d'avancement et si oui ou non l'action inclus une construction de rail
     * Dans le cas d'action auxilière il contiendra un int pour précisé l'action a effectuer
     * 
     * @param int actionId id de l'action a réaliser
     * @param array details liste des details de l'action
     */
    public function play(RRAction $action, array $details)
    {
        if($details['nb'] == 1){
            $this->workers = $this->workers-$action->getWorkerCost();
        }
        
        switch($action->getActionType()){
            case ActionType::railConstruction:
                switch($details["rail"]){
                    case RailType::grey:
                        if($this->activatedRailwayBonus[RailwayType::transsiberian][1]){
                            $this->railways[$details["railway"]]->setRailSlot($details["pos"], $details["rail"]);
                        }
                        break;
                    case RailType::brown:
                        if($this->activatedRailwayBonus[RailwayType::transsiberian][5]){
                            $this->railways[$details["railway"]]->setRailSlot($details["pos"], $details["rail"]);
                        }
                        break;
                    case RailType::beige:
                        if($this->activatedRailwayBonus[RailwayType::transsiberian][9]){
                            $this->railways[$details["railway"]]->setRailSlot($details["pos"], $details["rail"]);
                        }
                        break;
                    case RailType::white:
                        if($this->activatedRailwayBonus[RailwayType::transsiberian][14]){
                            $this->railways[$details["railway"]]->setRailSlot($details["pos"], $details["rail"]);
                        }
                        break;
                    default:
                        $this->railways[$details["railway"]]->setRailSlot($details["pos"], $details["rail"]);
                        break;
                }
                $this->applyBonus();
                break;
            case ActionType::trainsFactories:
                //to do
                break;
            case ActionType::industry:
                //to do
                break;
            case ActionType::auxiliaries:
                //to do
                break;
        }
    }

    /**
     * Lorsque qu'un rail est construit, cette action peut déclencher l'obtention
     * d'un bonus, cette méthode permet d'appliquer ces bonus.
     *  
     */
    public function applyBonus()
    {
        // on gère le chemin transsibérien
        //d'abord on récupère l'état du chemin de fer
        $tmpRailway = $this->railways[RailwayType::transsiberian]->getRailSlots();
        //$trainPower = $this->trains[0] -> getPower() + $this->trains[1]->getPower();
        $trainPower = 0;

        //ensuite on vérifie
        foreach($this->activatedRailwayBonus[RailwayType::transsiberian] as $slot => $activated)
        {
            if(!$activated)
            {
                if($slot == 2)
                {
                    if($tmpRailway[RailType::brown] >= $slot )
                    {
                        if($trainPower >= $slot){
                            //On met juste le boolean a true, il sera utiliser lors de la reinitialisation des ouvriers
                            $this->activatedRailwayBonus[RailwayType::transsiberian][$slot] = true;
                        }
                    }
                } else {
                    if($tmpRailway[RailType::black] >= $slot)
                    {
                        // do the bonus
                        switch($slot){
                            //On le met juste a true, il sera utiliser lors de la constrution de rail
                            case 1:
                                $this->activatedRailwayBonus[RailwayType::transsiberian][$slot] = true;
                                break;
                            case 5:
                                $this->activatedRailwayBonus[RailwayType::transsiberian][$slot] = true;
                                break;
                            case 9:
                                $this->activatedRailwayBonus[RailwayType::transsiberian][$slot] = true;
                            case 12:
                                if($trainPower >= $slot){
                                    //pick a bonus
                                }
                                break;
                            case 14:
                                //permet de débloquer les rail blanc
                                $this->activatedRailwayBonus[RailwayType::transsiberian][$slot] = true;
                                //ask for rail
                                $this->victoryPointsScale += 10;
                        }
                    }
                }
            }
        }

        //on gère st Peterbourg
        //$trainPower = $this->trains[2];
        $tmpRailway = $this->railways[RailwayType::stPetersburg]->getRailSlots();

        foreach($this->activatedRailwayBonus[RailwayType::stPetersburg] as $slot => $activated)
        {
            if(!$activated)
            {
                if($slot == 6){
                    if($tmpRailway[RailType::grey] >= $slot){
                        if($trainPower >= $slot){
                            //on le met juste a true, il sera utiliser lors du compte des points
                            $this->activatedRailwayBonus[RailwayType::stPetersburg][$slot] = true;
                        }
                    }
                } else {
                    if($tmpRailway[RailType::black] >= $slot){
                        if($trainPower >= $slot){
                            //pick a bonus
                            $this->activatedRailwayBonus[RailwayType::stPetersburg][$slot] = true;
                        }
                    }
                }
            }
        }

        //On gère kiev
        //$trainPower = $this->train[3];
        $tmpRailway = $this->railways[RailwayType::kiev]->getRailSlots();

        foreach($this->activatedRailwayBonus[RailwayType::kiev] as $slot => $activated)
        {
            if(!$activated){
                if($slot == 4 && $this->activeBonusToken[5]){
                    if($tmpRailway[RailType::grey] >= $slot && $trainPower >= $slot){
                        $this->activatedRailwayBonus[RailwayType::kiev][$slot] = true;
                    }
                } else {
                    if($slot == 6 && $tmpRailway[RailType::black] >= $slot){
                        $this->activatedRailwayBonus[RailwayType::kiev][$slot] = true;
                    } else if($slot == 8 && $tmpRailway[RailType::black] >= $slot){
                        $this->victoryPointsScale += 10;
                    } else {
                        if($trainPower >= $slot && $tmpRailway[RailType::black] >= $slot){
                            $this->activatedRailwayBonus[RailwayType::kiev][$slot] = true;
                        }
                    }
                    
                }
            }
        }
        //repeat for the other rails

    }
}
?>