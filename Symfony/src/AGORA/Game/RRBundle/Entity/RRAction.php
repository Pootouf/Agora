<?php

namespace AGORA\Game\RRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Classe représentant une action possible dans une partie de Russian Railroads
 * 
 * @ORM\Table(name="rr_action")
 * @ORM\Entity(repositoryClass="AGORA\Game\RRBundle\Repository\RRActionRepository")
 */
class RRAction{

    /** 
     * @var int
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * Chaine de caractère décrivant l'action avec précision.
     * 
     * @var string 
     * 
     * @ORM\Column(name="identification", type="text")
     */
    private string $identification;

    /**
     * Ce booléen indique si un joueur a déjà jouer sur cette action et donc l'a rendu innaccesible
     * 
     * @var bool
     * 
     * @ORM\Column(name="isReserved", type="boolean")
     */
    private bool $isReserved;

    /**
     * Représente le type d'action. Ce type doit être l'une des valeurs présente dans ActionType.
     * 
     * @var ActionType
     * 
     * @ORM\Column(name="actionType", type="integer")
     */
    private int $actionType;

    /**
     * Nombre d'ouvrier nécessaire pour la réalisation de l'action.
     * 
     * @var int
     * 
     * @ORM\Column(name="workerCost",type="integer")
     */
    private int $workerCost;

    /**
     * Determine si un rouble est nécessaire pour la réalisation de cette action.
     * 
     * @var bool
     * 
     * @ORM\Column(name="isRubble", type="boolean")
     */
    private bool $isRubleNeed;

    /**
     * Definit tout les détails de l'action. Type de rail a construire, nombre de train etc...
     * 
     * @var Array
     * 
     * @ORM\Column(name="details", type="array")
     */
    private Array $details;

    static int $allId = 0;

    /**
     * Constructeur d'une action. 
     */
    public function __construct(int $id,string $ident, bool $reserved, int $actionT, int $workerNeeded, bool $rubleNeeded, Array $newDetails){
        // $this->id = RRAction::$allId;
        // RRAction::$allId ++;
        $this->id = $id;
        $this->identification = $ident;
        $this->isReserved = $reserved;
        $this->actionType = $actionT;
        $this->workerCost = $workerNeeded;
        $this->isRubleNeed = $rubleNeeded;
        $this->details = $newDetails;
    }

    /**
     * Accesseur de l'id de l'action
     * 
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Accesseur du type d'action
     * 
     * @return int Le type d'action 
     */
    public function getActionType(){
        return $this->actionType;
    }

    /**
     * Accesseur de l'identification de l'action.
     * 
     * @return string L'identification de l'action.
     */
    public function getIdentification(){
        return $this->identification;
    }

    /**
     * Accesseur de si l'action à déjà été jouer durant cette manche.
     * 
     * @return bool true si l'action déjà été jouer, false sinon.
     */
    public function isReserved(){
        return $this->isReserved;
    }

    /**
     * Accesseur du nombre d'ouvrier neccessaire pour réaliser cette action
     * 
     * @return int Nombre d'ouvrier requis pour l'action
     */
    public function getWorkerCost(){
        return $this->workerCost;
    }

    /**
     * Accesseur de si l'action nécessite un rouble pour être executé.
     * 
     * @return bool true si l'action nécessite un rouble, false sinon.
     */
    public function isRubleNeeded(){
        return $this->isRubleNeed;
    }

    /**
     * Accesseur des details de l'action
     * 
     * @return array Les details de l'action.
     */
    public function getDetails(){
        return $this->details;
    }

    /**
     * Accesseur en écriture du nombre d'ouvrier nécessaire pour cette action
     *
     * @param int newCost Nouveau coût pour cette action
     */
    public function setWorkerCost(int $newCost){
        $this->workerCost = $newCost;
    }

    /**
     * Accesseur en écriture de si un rouble es nécessaire pour cette action
     * 
     * @param bool rubleNeeded Booléen determinant si un rouble est nécessaire.
     */
    public function setRubleNeeded(bool $rubleNeeded){
        $this->isRubleNeed =$rubleNeeded;
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
     * Accesseur en écriture permettant de définir si l'action a été jouer ou non
     * 
     * @param bool state nouvelle état dans lequel ce trouve l'action.
     */
    public function setreserved(bool $state){
        $this->isReserved = $state;
    }

    /**
     * Accesseur en écriture pour l'identificateur de l'action.
     * 
     * @param string newID Chaine de caratère définisant l'action.
     */
    public function setIdentification(string $newID){
        $this->identification = $newID;
    }

    /**
     * Accesseur en écriture pour les details de l'action
     * 
     * @param array newDetails tableau associatif contenant les details de l'action. Le contenu du tableau peut varier en fonction du type de l'action.
     */
    public function setDetails(array $newDetails){
        $this->details = $newDetails;
    }

    public static function compare_RRActions($rra1,$rra2) {
        return $rra1->getId() - $rra2->getId(); 
    }

}

?>