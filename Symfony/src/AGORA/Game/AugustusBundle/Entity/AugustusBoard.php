<?php

namespace AGORA\Game\AugustusBundle\Entity;

use AGORA\Game\AugustusBundle\Entity\AugustusGame;
use AGORA\Game\AugustusBundle\Entity\AugustusCard;
use AGORA\Game\AugustusBundle\Entity\AugustusToken;
use AGORA\Game\AugustusBundle\Entity\AugustusColor;
use AGORA\Game\AugustusBundle\Entity\AugustusResource;
use AGORA\Game\AugustusBundle\Entity\AugustusPower;


use Doctrine\ORM\Mapping as ORM;

/**
 * AugustusBoard
 *
 * @ORM\Table(name="augustus_board")
 * @ORM\Entity(repositoryClass="AGORA\Game\AugustusBundle\Repository\AugustusBoardRepository")
 */
class AugustusBoard {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * 
     * @ORM\OneToMany(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusCard", mappedBy="board", cascade={"persist"})
     */
    private $deck;


    /**
     * @ORM\OneToOne(targetEntity="AGORA\Game\AugustusBundle\Entity\AugustusGame", inversedBy="board", cascade={"persist"})
     */
    private $game;
    
    /**
     * @var array
     *
     * @ORM\Column(name="token_bag", type="array")
     */
    private $tokenBag;
    
    // Le constructeur.
    public function __construct(AugustusGame $gameId) {
        $this->game = $gameId;

        // Il y a 88 objectifs (cartes) dans le deck au debut d'une partie.
        $this->deck = new \Doctrine\Common\Collections\ArrayCollection();
        
        // La liste toCapture est la liste utilisée pour savoir quels sont les token nécéssaire à la capture de la carte.
        $toCapture = new \Doctrine\Common\Collections\ArrayCollection();
        
        // Ajout des cartes dans le deck.
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $this->deck->add(new AugustusCard($this, 1, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 3, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $this->deck->add(new AugustusCard($this, 2, AugustusColor::GREEN, AugustusResource::NORESOURCE, 3, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $this->deck->add(new AugustusCard($this, 3, AugustusColor::PINK, AugustusResource::NORESOURCE, 6, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $this->deck->add(new AugustusCard($this, 4, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 6, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 5, AugustusColor::GREEN, AugustusResource::WHEAT, 3, AugustusPower::ONELEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 6, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 3, AugustusPower::ONELEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 7, AugustusColor::GREEN, AugustusResource::WHEAT, 0, AugustusPower::ONEPOINTBYDOUBLESWORD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 8, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::ONEPOINTBYDOUBLESWORD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $this->deck->add(new AugustusCard($this, 9, AugustusColor::GREEN, AugustusResource::NORESOURCE, 3, AugustusPower::DOUBLESWORDISSHIELD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $this->deck->add(new AugustusCard($this, 10, AugustusColor::PINK, AugustusResource::WHEAT, 3, AugustusPower::DOUBLESWORDISSHIELD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 11, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::ONEPOINTBYSHIELD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 12, AugustusColor::GREEN, AugustusResource::NORESOURCE, 0, AugustusPower::ONEPOINTBYSHIELD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 13, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 3, AugustusPower::TWOLEGIONONDOUBLESWORD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 14, AugustusColor::GREEN, AugustusResource::NORESOURCE, 3, AugustusPower::TWOLEGIONONDOUBLESWORD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 15, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 3, AugustusPower::TWOLEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 16, AugustusColor::GREEN, AugustusResource::NORESOURCE, 3, AugustusPower::TWOLEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $this->deck->add(new AugustusCard($this, 17, AugustusColor::PINK, AugustusResource::NORESOURCE, 8, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $this->deck->add(new AugustusCard($this, 18, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 8, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 19, AugustusColor::GREEN, AugustusResource::NORESOURCE, 3, AugustusPower::TWOLEGIONONTEACHES, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 20, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 3, AugustusPower::TWOLEGIONONTEACHES, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 21, AugustusColor::PINK, AugustusResource::NORESOURCE, 9, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 22, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 9, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 23, AugustusColor::GREEN, AugustusResource::NORESOURCE, 10, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 24, AugustusColor::ORANGE, AugustusResource::GOLD, 10, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 25, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::TWOPOINTBYCHARIOT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 26, AugustusColor::GREEN, AugustusResource::GOLD, 0, AugustusPower::TWOPOINTBYCHARIOT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 27, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::THREEPOINTBYCATAPULT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 28, AugustusColor::PINK, AugustusResource::NORESOURCE, 0, AugustusPower::THREEPOINTBYCATAPULT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 29, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 4, AugustusPower::TWOLEGIONONSHIELD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 30, AugustusColor::GREEN, AugustusResource::NORESOURCE, 4, AugustusPower::TWOLEGIONONSHIELD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 31, AugustusColor::PINK, AugustusResource::NORESOURCE, 4, AugustusPower::SHIELDISCHARIOT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 32, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 4, AugustusPower::SHIELDISCHARIOT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 33, AugustusColor::GREEN, AugustusResource::NORESOURCE, 5, AugustusPower::TWOLEGIONONKNIFE, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 34, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 5, AugustusPower::TWOLEGIONONKNIFE, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 35, AugustusColor::PINK, AugustusResource::NORESOURCE, 5, AugustusPower::ONECARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 36, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 5, AugustusPower::ONECARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 37, AugustusColor::GREEN, AugustusResource::NORESOURCE, 6, AugustusPower::REMOVEONELEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $this->deck->add(new AugustusCard($this, 38, AugustusColor::ORANGE, AugustusResource::NORESOURCE, 6, AugustusPower::REMOVEONELEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 39, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 11, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 40, AugustusColor::GREEN, AugustusResource::WHEAT, 11, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 41, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 2, AugustusPower::REMOVETWOLEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 42, AugustusColor::GREEN, AugustusResource::NORESOURCE, 2, AugustusPower::REMOVETWOLEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 43, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 2, AugustusPower::MOVELEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 44, AugustusColor::PINK, AugustusResource::NORESOURCE, 2, AugustusPower::MOVELEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 45, AugustusColor::PINK, AugustusResource::GOLD, 6, AugustusPower::ONELEGIONONANYTHING, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 46, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 6, AugustusPower::ONELEGIONONANYTHING, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 47, AugustusColor::GREEN, AugustusResource::NORESOURCE, 0, AugustusPower::TWOPOINTBYGREENCARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 48, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::TWOPOINTBYSENATORCARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 49, AugustusColor::PINK, AugustusResource::NORESOURCE, 0, AugustusPower::FOURPOINTBYPINKCARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 50, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 12, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 51, AugustusColor::GREEN, AugustusResource::NORESOURCE, 5, AugustusPower::TWOLEGIONONCHARIOT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 52, AugustusColor::ORANGE, AugustusResource::NORESOURCE, 5, AugustusPower::TWOLEGIONONCHARIOT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 53, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::THREEPOINTBYTEACHES, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 54, AugustusColor::GREEN, AugustusResource::NORESOURCE, 0, AugustusPower::THREEPOINTBYTEACHES, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 55, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 5, AugustusPower::CHARIOTISCATAPULT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 56, AugustusColor::PINK, AugustusResource::NORESOURCE, 5, AugustusPower::CHARIOTISCATAPULT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 57, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 13, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 58, AugustusColor::GREEN, AugustusResource::NORESOURCE, 13, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 59, AugustusColor::PINK, AugustusResource::NORESOURCE, 7, AugustusPower::TWOLEGIONONCATAPULT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $this->deck->add(new AugustusCard($this, 60, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 7, AugustusPower::TWOLEGIONONCATAPULT, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::KNIFE);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 61, AugustusColor::GREEN, AugustusResource::NORESOURCE, 14, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::KNIFE);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 62, AugustusColor::ORANGE, AugustusResource::NORESOURCE, 14, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 63, AugustusColor::GREEN, AugustusResource::GOLD, 0, AugustusPower::FOURPOINTBYKNIFE, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 64, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::FIVEPOINTBYREDCARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 65, AugustusColor::GREEN, AugustusResource::NORESOURCE, 0, AugustusPower::FOURPOINTBYKNIFE, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 66, AugustusColor::PINK, AugustusResource::NORESOURCE, 0, AugustusPower::FOURPOINTBYPINKCARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 67, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::TWOPOINTBYSENATORCARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 68, AugustusColor::GREEN, AugustusResource::BOTH, 0, AugustusPower::TWOPOINTBYGREENCARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 69, AugustusColor::ORANGE, AugustusResource::GOLD, 0, AugustusPower::SIXPOINTBYORANGECARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 70, AugustusColor::GREEN, AugustusResource::NORESOURCE, 3, AugustusPower::TWOLEGIONONANYTHING, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 71, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 0, AugustusPower::FIVEPOINTBYREDCARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 72, AugustusColor::GREEN, AugustusResource::NORESOURCE, 3, AugustusPower::TWOLEGIONONANYTHING, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 73, AugustusColor::PINK, AugustusResource::NORESOURCE, 7, AugustusPower::CATAPULTISTEACHES, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 74, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 7, AugustusPower::CATAPULTISTEACHES, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 75, AugustusColor::GREEN, AugustusResource::NORESOURCE, 5, AugustusPower::REMOVEALLLEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 76, AugustusColor::ORANGE, AugustusResource::NORESOURCE, 5, AugustusPower::REMOVEALLLEGION, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 77, AugustusColor::PINK, AugustusResource::NORESOURCE, 15, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $this->deck->add(new AugustusCard($this, 78, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 15, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 79, AugustusColor::GREEN, AugustusResource::NORESOURCE, 16, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 80, AugustusColor::PINK, AugustusResource::NORESOURCE, 16, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 81, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 5, AugustusPower::REMOVEONECARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::DOUBLESWORD);
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CHARIOT);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 82, AugustusColor::PINK, AugustusResource::NORESOURCE, 5, AugustusPower::REMOVEONECARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 83, AugustusColor::ORANGE, AugustusResource::NORESOURCE, 0, AugustusPower::SIXPOINTBYORANGECARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::SHIELD);
        $toCapture->add(AugustusToken::CATAPULT);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 84, AugustusColor::GREEN, AugustusResource::NORESOURCE, 12, AugustusPower::NOPOWER, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 85, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 10, AugustusPower::TEACHESISKNIFE, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 86, AugustusColor::PINK, AugustusResource::WHEAT, 10, AugustusPower::TEACHESISKNIFE, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 87, AugustusColor::ORANGE, AugustusResource::BOTH, 4, AugustusPower::COMPLETECARD, $toCapture->toArray()));
        
        $toCapture->clear();
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::TEACHES);
        $toCapture->add(AugustusToken::KNIFE);
        $toCapture->add(AugustusToken::KNIFE);
        $this->deck->add(new AugustusCard($this, 88, AugustusColor::SENATOR, AugustusResource::NORESOURCE, 6, AugustusPower::COMPLETECARD, $toCapture->toArray()));
        
        // Ensuite on mélange le deck, il y a 88 cartes donc un swap de 150 paires semble correcte pour un mélange.
        for ($i = 0; $i < 150; $i++) {
          $nbAlea1 = rand_int(0, 87);
          $nbAlea2 = rand_int(0, 87);
          $transiCard1 = $this->deck->get($nbAlea1);
          $transiCard2 = $this->deck->get($nbAlea2);
          $this->deck->remove($nbAlea1);
          $this->deck->remove($nbAlea1);
          $this->deck->set($nbAlea2, $transiCard1);
          $this->deck->set($nbAlea1, $transiCard2);
        }
        
        // Il y a 23 jetons (tokens) dans le sac de token au début d'une partie.
        $tokenBag = new \Doctrine\Common\Collections\ArrayCollection();
        
        // Ajout des tokens dans le sac.
        for ($i = 0; $i < 6; $i++) {
          $tokenBag->add(AugustusToken::DOUBLESWORD);
        }
        for ($i = 0; $i < 5; $i++) {
          $tokenBag->add(AugustusToken::SHIELD);
        }
        for ($i = 0; $i < 4; $i++) {
          $tokenBag->add(AugustusToken::CHARIOT);
        }
        for ($i = 0; $i < 3; $i++) {
          $tokenBag->add(AugustusToken::CATAPULT);
        }
        for ($i = 0; $i < 2; $i++) {
          $tokenBag->add(AugustusToken::TEACHES);
        }
        for ($i = 0; $i < 2; $i++) {
          $tokenBag->add(AugustusToken::JOKER);
        }
        $tokenBag->add(AugustusToken::KNIFE);
        
        // Ensuite on mélnge le sac de jeton, il y a 23 jeton donc un swap de 50 paires semble correct pour un mélange.
        for ($i = 0; $i < 50; $i++) {
          $nbAlea1 = rand(0, 22);
          $nbAlea2 = rand(0, 22);
          $transiToken1 = $tokenBag->get($nbAlea1);
          $transiToken2 = $tokenBag->get($nbAlea2);
          $tokenBag->remove($nbAlea1);
          $tokenBag->remove($nbAlea1);
          $tokenBag->set($nbAlea2, $transiToken1);
          $tokenBag->set($nbAlea1, $transiToken2);
        }
        
        $this->tokenBag = $tokenBag->toArray();
        // Il y a 5 objectifs sur le terrain en début d'une partie.
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
   * Add card to deck.
   *
   * @param \AGORA\Game\AugustusBundle\Entity\Card $card
   *
   * @return boolean TRUE if this element is added, FALSE otherwise.
   */
  public function addCardToDeck(AugustusCard $card)
  {
      return $this->deck->add($card);
  }

  /**
   * Remove card from deck.
   *
   * @param \AGORA\Game\AugustusBundle\Entity\Card $card
   *
   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
   */
  public function removeCardFromDeck(AugustusCard $card)
  {
	 return $this->deck->removeElement($card);
  }

  /**
   * Get deck.
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getDeck()
  {
      return $this->deck;
  }

  /**
   * Get obj line.
   *
   * @return array
   */
  public function getObjLine()
  {
    $objLine = [];
    $deck = $this->getDeck()->toArray();

    $card = array_pop($deck);

    while (count($objLine) != 5 && $card) {
      if ($card->getIsInLine()) {
        array_push($objLine, $card);
      }
      $card = array_pop($deck);
    }

    // $j = count($deck) - 1;
    // $i = 0;
    // while ($i < 5 && $j != 0) {
    //   if ($deck[$j]->getIsInLine()) {
    //       $objLine[] = $deck[$j]; 
    //       $i = $i + 1;
    //   }
    //   $j = $j - 1;
    // }
    return $objLine;
  }
  
  /**
   * Add token to bag.
   *
   * @param \AGORA\Game\AugustusBundle\Entity\Token $token
   *
   * @return boolean TRUE if this element is added, FALSE otherwise.
   */
  public function addTokenToBag(string $token)
  {
      return $this->tokenBag->add($token);
  }
  
  /**
   * Add token to bag with index.
   *
   * @params int $index, \AGORA\Game\AugustusBundle\Entity\Token $token
   *
   * @return boolean TRUE if this element is added, FALSE otherwise.
   */
  public function addTokenToBagWithIndex(int $index, string $token)
  {
      return $this->tokenBag->set($index, $token);
  }

  /**
   * Remove token from bag.
   *
   * @param \AGORA\Game\AugustusBundle\Entity\Token $token
   *
   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
   */
  public function removeTokenFromBag(string $token)
  {
      return $this->tokenBag->removeElement($token);
  }

  /**
   * Remove token from bag with index.
   *
   * @param int $index
   * 
   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
   */
  public function removeTokenFromBagWithIndex(int $index)
  {
      return $this->tokenBag->remove($index);
  }
  
  /**
   * Get token bag.
   *
   * @return array
   */
  public function getTokenBag()
  {
      return $this->tokenBag;
  }
  
  /**
   * Clear token bag.
   *
   * @return array
   */
  public function clearTokenBag()
  {
      $this->tokenBag = array();
      return $this->tokenBag;
  }

    /**
     * Set tokenBag.
     *
     * @param array $tokenBag
     *
     * @return AugustusBoard
     */
    public function setTokenBag($tokenBag)
    {
        $this->tokenBag = $tokenBag;

        return $this;
    }

    /**
     * Add deck.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusCard $deck
     *
     * @return AugustusBoard
     */
    public function addDeck(AugustusCard $deck)
    {
        $this->deck[] = $deck;
        $deck->setBoard($this);
        return $this;
    }

    /**
     * Remove deck.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusCard $deck
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDeck(AugustusCard $deck)
    {
        $deck->setBoard(null);
        return $this->deck->removeElement($deck);
    }

    /**
     * Clear deck.
     *
     * @return array
     */
    public function clearDeck()
    {
        $this->deck = array();
        return $this->deck;
    }

    /**
     * Set game.
     *
     * @param \AGORA\Game\AugustusBundle\Entity\AugustusGame|null $game
     *
     * @return AugustusBoard
     */
    public function setGame(AugustusGame $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game.
     *
     * @return \AGORA\Game\AugustusBundle\Entity\AugustusGame|null
     */
    public function getGame()
    {
        return $this->game;
    }
}
