<?php

namespace AGORA\Game\RRBundle\Entity;

use AGORA\Game\RRBundle\Model\ActionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/** 
 * Cette classe représente l'état d'une partie de Russian Railroads
 * 
 * @ORM\Table(name="rr_game")
 * @ORM\Entity(repositoryClass="AGORA\Game\RRBundle\Repository\RRGameRepository")
*/
class RRGame{


    /**
     * @var int
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * Tableau contenant tout les joueurs de à la partie.
     * 
     * @var array
     * 
     * @ORM\Column(name="players", type="array")
     */
    private array $players;

    //prévoir un attribut pour le changement d'ordre du jeu

    /**
     * Nombre de joueur requis pour cette partie. La partie ne commencera pas tant que le nombre de joueur ayant rejoins n'est pas égale a ce nombre.
     * 
     * @var int
     * 
     * @ORM\Column(name="nbPlayer", type="integer")
     */
    private int $nbPlayer;


    /**
     * Plateau commun de la partie.
     * 
     * @ORM\OneToOne(targetEntity="RRBoard")
     * @ORM\JoinColumn(name="board", referencedColumnName="id")
     */
    private RRBoard $board;
    
    /**
     * Id du joueur dont c'est le tour 
     * 
     * @ORM\Column(name="current_player_id", type="integer")

     */
    private $currentPlayer;

    /**
     * Nombre de tour restant avant la fin de la partie
     * 
     * @var int
     * 
     * @ORM\Column(name="round_last", type="integer")
     */
    private int $roundLast;

    /**
     * Indique si la partie a commencé.
     * 
     * @var bool
     * 
     * @ORM\Column(name="started", type="boolean")
     */
    private bool $started;

    /**
     * Constructeur de la partie. Il va initialisé le tableau des joueurs a vide.
     * 
     * @param int nbOfPlayer nombre de joueur requis pour cette partie
     */
    public function __construct(int $nbOfPlayer, $board) {
        $this->nbPlayer = $nbOfPlayer;
        $this->players = array();
        $this->currentPlayer = -1;
        $this->started = false;
        $this->roundLast = $nbOfPlayer >= 3 ? 7 : 6;
        $this->board = $board;
    }

    /**
     * Méthode qui va lancer la partie, en définissant les rôle, gérant les bonus de début de partie et en se méttant en attente d'action des joueurs
     */
    public function startGame()
    {
        /*if(count($this->players) == $this->nbPlayer){


            $this->started = true;
        }*/

        // on randomize l'ordre et on génère le plateau
        $this->roundLast = 7;
        $this->started = true;
        $this->currentPlayer = $this->players->get(0)->getId();
    }

    /**
     * Methode permettant d'ajouter un nouveau joueur dans la partie.
     * 
     * @param RRPlayer newPlayer Joueur enregistré dans la partie.
     */
    public function addPlayer(RRPlayer $newPlayer)
    {
        array_push($this->players, $newPlayer);
        // $this->players[] = $newPlayer;
        
    }

    /**
     * Accesseur de l'identifiant de la partie.
     * 
     * @return int identifiant de la partie.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Accesseur du nombre de joueur requis pour la partie.
     * 
     * @return int le nombre de joueur requis pour la partie.
     */
    public function getNbPlayer()
    {
        return $this->nbPlayer;
    }

    /**
     * Accesseur des joueurs connecté 
     * 
     * @return array Liste des joueurs connecté.
     */
    public function getPlayers()
    {
        return $this->players;
    }

    public function getPlayer(int $playerID)
    {
        foreach($this->players as $player)
        {
            if($player->getId() == $playerID){
                return $player;
            }
        }
    }

    /**
     * Accesseur du plateau commun
     * 
     * @OneToOne(targetEntity="AGORA\Game\RRBundle\Entity\RRBoard", cascade={"persist"})
     * @return RRBoard Plateau commun de la partie
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Accesseur en écriture du plateau commun.
     * 
     * @param RRBoard newBoard Nouveau plateau commun.
     */
    public function setBoard(RRBoard $newBoard)
    {
        $this->board = $newBoard;
    }

    /**
     * Accesseur en écriture de la liste des joueurs connecté.
     * 
     * @param array newPlayers Nouvelle list des joueurs connecté.
     */
    public function setPlayers(array $newPlayers)
    {
        $this->players = $newPlayers;
    }

    //TODO
   /* public function canPlay(int $playerId, int $actionId, array $details)
    {
        $action = $this->board->getAction($actionId);
        foreach($this->players as $p)
        {
            if($playerId == $p->getId())
            {
                switch($action->getActionType())
                {
                    case ActionType::railConstruction: 
                        if()
                }
            }
        }
    }*/

    /**
     * Fonction permettant de savoir si la partie est finie.
     * Une partie est finit lorsque l'ensemble des tours on été jouée.
     * 
     * @return boolean
     */
    public function isGameFinished()
    {
        if($this->roundLast == 0){
            foreach($this->players as $player){
                if(!$player->asPassed()){
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function turn(int $playerId, int $actionId, array $details)
    {
        if($this->started && $this->currentPlayer == $playerId){
            
            foreach($this->players->toArray() as $player){
                if($player->getid() == $playerId){
                    //On passe le tour
                    if($actionId == -1){
                        $player->setPassed(true);
                        return 0;
                    } else {
                        $action = $this->board->getAction($actionId);
            
                        if(!$action->isReserved()){
                            $details['rail'] = $action->getDetails()['railType'];
                            if($player->getWorker() >= $action->getWorkerCost()){

                                if($action->getActionType() == ActionType::railConstruction){
                                    $pos = $player->getRailWays()[$details['railway']]->getRailSlots()[$details['rail']];
                                    $details['pos'] = $pos+1;
                                }

                                $player->play($action, $details);
                                if(!strcmp($action->getIdentification(),"Construire 1 rail noir ou 1 rail gris") && $details['nb'] == 1){
                                    $action->setreserved(true);
                                }
                                if($player->getRubles() == 0 && $player->getWorker() == 0){
                                    $player->setPassed(false);
                                }

                                return 0;
                            } else {
                                //normalement on doit utiliser les roubles mais on verra après
                                return -2;
                            }
                        } else {
                            //l'action est déjà réservé
                            return -3;
                        }
                    }
                    $this->round();
                    break;
                }

            }
            
        } else {
            //ça n'est pas ton tour!
            return -1;
        }
    }

    /**
     * Méthode permettant d'effectuer la fin d'une manche et de commencer la prochaine.
     */
    public function round(){
        foreach($this->players->toArray() as $player){
            if(!$player->asPassed()){
                return;
            }
        }

        //Gain de point en fonction de l'ordre de jeu
        for($i = 1; $i < count($this->players); $i++ ){
            if(isset($this->players[$i])){
                $this->players[$i]->gainPoint($i + 1);
            }
        }
        // on compte les points

        // chemin de fer + industrialisaton

        //on redonne les travailleurs
        foreach($this->players->toArray as $player){
            $player->calculatePoints();
            $player->setPassed(false);
        }
             
        //on actualiser les conducteurs
        //TODO     
        
        $this->roundLast -= 1;
    }
}
?>