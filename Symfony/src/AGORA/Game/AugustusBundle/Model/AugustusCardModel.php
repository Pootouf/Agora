<?php

namespace AGORA\Game\AugustusBundle\Model;

use AGORA\Game\AugustusBundle\Entity\AugustusCard;
use AGORA\Game\AugustusBundle\Entity\AugustusToken;
use AGORA\Game\AugustusBundle\Entity\AugustusPower;
use Doctrine\ORM\EntityManager;

//Fonction agissant sur AugustusCard
class AugustusCardModel {

    protected $manager;
    
    //On construit notre api avec un entity manager permettant l'accès à la base de données
    public function __construct(EntityManager $em) {
        $this->manager = $em;
    }

    //Passe le token token de la liste des Tokens a celle des Tokens capturés.
    public function captureToken($idCard, $token) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $tokens = $card->getTokens();
        $ctrl = $card->getCtrlTokens();

        $ind = 0;
        while($ind < count($tokens)) {
            if ($tokens[$ind] == $token) {
                if ($ctrl[$ind] == false) {
                    $ctrl[$ind] = true;
                    $card->setCtrlTokens($ctrl);
                    $this->manager->flush();
                    return;
                }
            }
            $ind = $ind + 1;
        }
    }

    //Passe le token d'id idToken de la liste des Tokens capturés a celle des Tokens.
    public function getBackToken($idCard, $token) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $tokens = $card->getTokens();
        $ctrl = $card->getCtrlTokens();

        $ind = 0;
        while($ind < count($tokens)) {
            if ($tokens[$ind] == $token) {
                if ($ctrl[$ind] == true) {
                    $ctrl[$ind] = false;
                    $card->setCtrlTokens($ctrl);
                    $this->manager->flush();
                    return;
                }
            }
            $ind = $ind + 1;
        }
    }

    //La liste des Tokens est elle vide ?
    public function isCapturable($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $res = true;
        foreach($card->getCtrlTokens() as $c) {
            if ($c == false) {
                $res = false;
                break;
            }
        }

        return $res;
    }

    //Effectue le pouvoir lié a son type de pouvoir.
    public function doPower($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        switch($card->getPower()) {
            case AugustusPower::ONELEGION:
                $this->doOneLegion($idCard);
                break;
            case AugustusPower::TWOLEGION: 
                $this->doTwoLegion($idCard);
                break;
            case AugustusPower::DOUBLESWORDISSHIELD: 
                $this->doDoubleSwordIsShield($idCard);
                break;
            case AugustusPower::SHIELDISCHARIOT: 
                $this->doShieldIsChariot($idCard);
                break;
            case AugustusPower::CHARIOTISCATAPULT: 
                $this->doChariotIsCatapult($idCard);
                break;
            case AugustusPower::CATAPULTISTEACHES: 
                $this->doCatapultIsTeaches($idCard);
                break;
            case AugustusPower::TEACHESISKNIFE: 
                $this->doTeachesIsKnife($idCard);
                break;
        }
        $this->manager->flush();
    }

    private function doOneLegion($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $player = $card->getPlayerCtrl();
        $player->setLegionMax($player->getLegionMax() + 1);
        $player->setLegion($player->getLegion() + 1);
    }

    private function doTwoLegion($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $player = $card->getPlayerCtrl();
        $player->setLegionMax($player->getLegionMax() + 2);
        $player->setLegion($player->getLegion() + 2);
    }

    private function  doDoubleSwordIsShield($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $player = $card->getPlayerCtrl();
        $equivalences = $player->getEquivalences();
        if(count($equivalences[AugustusToken::SHIELD]) == 0) {
            $equivalences[AugustusToken::SHIELD] = [];
        }
        array_push($equivalences[AugustusToken::SHIELD], AugustusToken::DOUBLESWORD);
        if(count($equivalences[AugustusToken::DOUBLESWORD]) == 0) {
            $equivalences[AugustusToken::DOUBLESWORD] = [];
        }
        array_push($equivalences[AugustusToken::DOUBLESWORD], AugustusToken::SHIELD);
        $player->setEquivalences($equivalences);
    }

    private function  doShieldIsChariot($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $player = $card->getPlayerCtrl();
        $equivalences = $player->getEquivalences();
        if(count($equivalences[AugustusToken::SHIELD]) == 0) {
            $equivalences[AugustusToken::SHIELD] = [];
        }
        array_push($equivalences[AugustusToken::SHIELD], AugustusToken::CHARIOT);
        if(count($equivalences[AugustusToken::CHARIOT]) == 0) {
            $equivalences[AugustusToken::CHARIOT] = [];
        }
        array_push($equivalences[AugustusToken::CHARIOT], AugustusToken::SHIELD);
        $player->setEquivalences($equivalences);
    }

    private function doChariotIsCatapult($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $player = $card->getPlayerCtrl();
        $equivalences = $player->getEquivalences();
        if(count($equivalences[AugustusToken::CHARIOT]) == 0) {
            $equivalences[AugustusToken::CHARIOT] = [];
        }
        array_push($equivalences[AugustusToken::CHARIOT], AugustusToken::CATAPULT);
        if(count($equivalences[AugustusToken::CATAPULT]) == 0) {
            $equivalences[AugustusToken::CATAPULT] = [];
        }
        array_push($equivalences[AugustusToken::CATAPULT], AugustusToken::CHARIOT);
        $player->setEquivalences($equivalences);
    }

    private function doCatapultIsTeaches($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $player = $card->getPlayerCtrl();
        $equivalences = $player->getEquivalences();
        if(count($equivalences[AugustusToken::CATAPULT]) == 0) {
            $equivalences[AugustusToken::CATAPULT] = [];
        }
        array_push($equivalences[AugustusToken::CATAPULT], AugustusToken::TEACHES);
        if(count($equivalences[AugustusToken::TEACHES]) == 0) {
            $equivalences[AugustusToken::TEACHES] = [];
        }
        array_push($equivalences[AugustusToken::TEACHES], AugustusToken::CATAPULT);
        $player->setEquivalences($equivalences);
    }

    private function doTeachesIsKnife($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');

        $card = $cards->findOneById($idCard);

        $player = $card->getPlayerCtrl();
        $equivalences = $player->getEquivalences();
        if(count($equivalences[AugustusToken::TEACHES]) == 0) {
            $equivalences[AugustusToken::TEACHES] = [];
        }
        array_push($equivalences[AugustusToken::TEACHES], AugustusToken::KNIFE);
        if(count($equivalences[AugustusToken::KNIFE]) == 0) {
            $equivalences[AugustusToken::KNIFE] = [];
        }
        array_push($equivalences[AugustusToken::KNIFE], AugustusToken::TEACHES);
        $player->setEquivalences($equivalences);
    }

    public function ctrlTokenNb($idCard) {
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');
        $card = $cards->findOneById($idCard);

        $cpt = 0;
        foreach($card->getCtrlTokens() as $c) {
            if ($c == true) {
                $cpt += 1;
            }
        }

        return $cpt;
    }
}