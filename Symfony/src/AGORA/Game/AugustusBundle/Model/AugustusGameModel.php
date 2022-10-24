<?php

namespace AGORA\Game\AugustusBundle\Model;

use AGORA\Game\AugustusBundle\Entity\AugustusGame;
use AGORA\Game\AugustusBundle\Entity\AugustusPlayer;
use AGORA\Game\AugustusBundle\Entity\AugustusBoard;
use AGORA\Game\AugustusBundle\Entity\AugustusCard;
use AGORA\Game\AugustusBundle\Entity\AugustusToken;
use AGORA\Game\AugustusBundle\Entity\AugustusPower;
use AGORA\Game\AugustusBundle\Entity\AugustusColor;

use AGORA\Game\AugustusBundle\Model\AugustusBoardModel;
use AGORA\Game\AugustusBundle\Model\AugustusPlayerModel;
use AGORA\Game\AugustusBundle\Model\AugustusCardModel;

use AGORA\Game\GameBundle\Entity\Game;
use Doctrine\ORM\EntityManager;

class AugustusGameModel {

    protected $manager;
    public $boardModel;
    public $playerModel;
    public $cardModel;

    public function __construct(EntityManager $em) {
        $this->manager = $em;
        $this->boardModel = new AugustusBoardModel($em);
        $this->playerModel = new AugustusPlayerModel($em);
        $this->cardModel = new AugustusCardModel($em);
    }

    public function createGame($name, $playersNb, $user) {
        $augGame = new AugustusGame();
        $augGame->setBoard(new AugustusBoard($augGame));
        $this->manager->persist($augGame);
        $this->manager->flush();
        $this->boardModel->fillLine($augGame->getBoard()->getId());
        
        $game = new Game();
        $game->setGameId($augGame->getId());
        $gameInfoManager = $this->manager->getRepository('AGORAPlatformBundle:GameInfo');
        $gameInfo = $gameInfoManager->findOneBy(array('gameCode' => "aug"));
        $game->setGameInfoId($gameInfo);
        $game->setGameName($name);
        $game->setPlayersNb($playersNb);
        $game->setHostId($user);
        $game->setState("waiting");
        $game->setCreationDate(new \DateTime("now"));
        $this->manager->persist($game);
        $this->manager->flush();

        return $augGame->getId();
    }

    // donne une main de trois cartes à chaque joueur ainsi qu'un jeton sur le plateau
    public function initGame($id) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);

        $this->drawToken($id);

        foreach ($game->getPlayers() as $player) {
            for ($i = 0; $i < 3; $i++) {
                $player->addCard($this->boardModel->takeCard($game->getBoard()->getId()));
                $this->manager->flush();
            }
        }
        $game->setState("legion");
        $this->manager->flush();
    }

    // pioche un token dans son sac, si ce jeton est le joker, remet tout dans le sac de token
    public function drawToken($id) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);

        $game->setToken($this->boardModel->takeToken($game->getBoard()->getId()));
        if ($game->getToken() == AugustusToken::JOKER) {
            $this->boardModel->resetBag($game->getBoard()->getId());
        }
        $this->manager->flush();
    }

    // verifie que tous les joueurs ont vérouillé leur tour
    public function allOk($id) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);

        $ok = true;
        foreach ($game->getPlayers() as $player) {
            $ok = $ok && $player->getIsLock();
        }
        return $ok;
    }

    // penser à verif qu'un autre joueur n'a pas la recompense a faire dans le controleur
    public function claimReward($id, $playerId) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);
        $players = $this->manager->getRepository("AugustusBundle:AugustusPlayer");
        $player = $players->findOneById($playerId);

        $advantage = (count($player->getCtrlCards()) - 1) * 2;
        foreach ($game->getPlayers() as $gamer) {
            if ($gamer->getAdvantage() == $advantage) {
                $advantage = 0;
            }
        }

        if ($player->getAdvantage() == 0 && $advantage != 0) {
            $player->setAdvantage($advantage);
        }
        $this->manager->flush();
    }

    // verification que quelqu'un est arrivé à 7 carte controlé
    public function isGameOver($id){
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);

        foreach ($game->getPlayers() as $player) {
            if (count($player->getCtrlCards()) >= 7) {
                return true;
            }
        }
        return false;
    }

    // passe les parametre au jeu du prochain tour de jeu
    public function applyStep($id) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);

        switch($game->getState()) {
            case "legion":
                if ($this->allOk($id)) {
                    $this->drawToken($id);
                    $steps = $this->aveCesarSteps($id);
                    $states = $steps[0];
                    $affecteds = $steps[1];
                    $game->setState($states[0]);
                    $game->setAffectedPlayer($affecteds[0]);
                    $game->setNextStates(array_slice($states, 1));
                    $game->setNextAffecteds(array_slice($affecteds, 1));
                    $this->lockThem($id);
                }
                break;
            case "aveCesar":
                $card = $this->getCapturableCardFromPlayer($game->getAffectedPlayer());
                $this->playerModel->captureCard($game->getAffectedPlayer(), $card->getId());
                $this->changeGoldOwner($id, $game->getAffectedPlayer());
                $this->changeWheatOwner($id, $game->getAffectedPlayer());
                if ($game->getState() == "aveCesar") {
                    $this->cardModel->doPower($card->getId());
                }
                if ($this->playerModel->getNbOfCardColor($game->getAffectedPlayer(), $card->getColor()) == 3) {
                    $this->fillColorLoot($id, $game->getAffectedPlayer(), $card->getColor());
                }
                if ($this->playerModel->haveOneCardOfEach($game->getAffectedPlayer())) {
                    $this->fillColorLoot($id, $game->getAffectedPlayer(), "all");
                }
                if ($this->canClaimReward($id, $game->getAffectedPlayer())) {
                    $game->setState("takeLoot");
                    $this->lockThem($id);
                } else {
                    $game->setState($game->getNextStates()[0]);
                    $game->setAffectedPlayer($game->getNextAffecteds()[0]);
                    $game->setNextStates(array_slice($game->getNextStates(), 1));
                    $game->setNextAffecteds(array_slice($game->getNextAffecteds(), 1));
                    $this->lockThem($id);
                }
                break;
            case "takeLoot":
                $game->setState($game->getNextStates()[0]);
                $game->setAffectedPlayer($game->getNextAffecteds()[0]);
                $game->setNextStates(array_slice($game->getNextStates(), 1));
                $game->setNextAffecteds(array_slice($game->getNextAffecteds(), 1));
                $this->lockThem($id);
                break;
            case "waiting":
                $this->initGame($id);
                break;
            default:
                $this->nextStep($id);
                break;
        }


        if ($game->getState() == "removeOneCard") {
            foreach ($game->getPlayers() as $player) {
                $idp = $player->getId();
                if ($idp != $game->getAffectedPlayer()) {
                    $this->playerModel->deleteCtrlCard($idp);
                }
            }
            $game->setState($game->getNextStates()[0]);
            $game->setAffectedPlayer($game->getNextAffecteds()[0]);
            $game->setNextStates(array_slice($game->getNextStates(), 1));
            $game->setNextAffecteds(array_slice($game->getNextAffecteds(), 1));
            $this->lockThem($id);
        }
        if ($game->getState() == "endAveCesar") {
            $players = $game->getPlayers();
            foreach ($players as $player) {
                $player->setScore($this->getScores($id, $player->getId()));
            }
            if ($this->isGameOver($id)) {
                $winner = $this->getWinner($id);
                $game->setAffectedPlayer($winner->getId());
                $game->setState("endGame");
                foreach ($players as $player) {
                    $player->setIsWinner(($winner->getId() == $player->getId()) ? true : false);
                }
            } else {
                $game->setState("legion");
                $game->setAffectedPlayer(-1);
                $this->lockThem($id);
            }
        }

        $this->manager->flush();
    }

    // Utilisé pendant une phase ave cesar (applyStep)
    // Si le joueur actuel vient de récupérer une carte grace à un pouvoir
    // dans ce cas on met le pouvoir de celle-ci dans state
    // Sinon on passe au state suivant
    private function nextStep($id) {
        if ($this->allOk($id)) {
            $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
            $game = $games->findOneById($id);

            $card = $this->getCapturableCardFromPlayer($game->getAffectedPlayer());
            if ($card) {
                $game->setState("aveCesar");
                if ($this->isPowerWithAction($card->getId())) {
                    $game->setNextStates(array_merge(array($card->getPower()), $game->getNextStates()));
                    $game->setNextAffecteds(array_merge(array($game->getAffectedPlayer()), $game->getNextAffecteds()));
                }
            } else {
                if ($game->getState() != "endGame") {
                    $game->setState($game->getNextStates()[0]);
                    $game->setAffectedPlayer($game->getNextAffecteds()[0]);
                    $game->setNextStates(array_slice($game->getNextStates(), 1));
                    $game->setNextAffecteds(array_slice($game->getNextAffecteds(), 1));
                }
            }
            $this->lockThem($id);
        }
        $this->manager->flush();
    }

    // calcul et renvoie un tableau à deux dimensions avec:
    // tableau[0] = la suite d'états à prendre pour la phase AveCesar
    // tableau[1] = la suite de joueurs qui "activerons" ces états
    private function aveCesarSteps($id) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);

        $states = array();
        $affecteds = array();

        $capturer = array();
        $index = array();
        foreach ($game->getPlayers() as $player) {
            foreach ($this->cleanArray($player->getCards()->toArray()) as $card) {
                if ($this->cardModel->isCapturable($card->getId())) {
                    $capturer[$card->getNumber()] = $player->getId();
                    array_push($index, $card->getNumber());
                }
            }
        }
        sort($index);
        foreach ($index as $i) {
            array_push($states, "aveCesar");
            array_push($affecteds, $capturer[$i]);
            $card = $this->playerModel->getCardByNumber($capturer[$i], $i);
            if ($this->isPowerWithAction($card->getId())) {
                    array_push($states, $card->getPower());
                    array_push($affecteds, $capturer[$i]);
            }
        }
        if (empty($states)) {
            array_push($states, "legion");
        } else {
            array_push($states, "endAveCesar");
        }
        array_push($affecteds, -1);

        return array($states, $affecteds);
    }

    // Retourne un bouleen disant si la carte à un pouvoir qui necéssite
    // un state particulier
    private function isPowerWithAction($idCard) {
        $cards = $this->manager->getRepository("AugustusBundle:AugustusCard");
        $card = $cards->findOneById($idCard);

        $power = $card->getPower();
        return $power == AugustusPower::TWOLEGIONONDOUBLESWORD ||
            $power == AugustusPower::TWOLEGIONONTEACHES ||
            $power == AugustusPower::TWOLEGIONONSHIELD ||
            $power == AugustusPower::TWOLEGIONONKNIFE ||
            $power == AugustusPower::ONECARD ||
            $power == AugustusPower::REMOVEONELEGION ||
            $power == AugustusPower::REMOVETWOLEGION ||
            $power == AugustusPower::MOVELEGION ||
            $power == AugustusPower::ONELEGIONONANYTHING ||
            $power == AugustusPower::TWOLEGIONONCHARIOT ||
            $power == AugustusPower::TWOLEGIONONCATAPULT ||
            $power == AugustusPower::TWOLEGIONONANYTHING ||
            $power == AugustusPower::REMOVEALLLEGION ||
            $power == AugustusPower::REMOVEONECARD ||
            $power == AugustusPower::COMPLETECARD;
    }

    // retourne la carte où un joueur à réussi à placer toutes les légions
    // ou null
    private function getCapturableCardFromPlayer($playerId) {
        $players = $this->manager->getRepository("AugustusBundle:AugustusPlayer");
        $player = $players->findOneById($playerId);

        foreach ($this->cleanArray($player->getCards()->toArray()) as $card) {
            if ($this->cardModel->isCapturable($card->getId())) {
                return $card;
            }
        }

        return null;
    }

    // rempli le taleau de loot
    public function fillColorLoot($id, $playerId, $type) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);
        $colorLoot = $game->getColorLoot();

        if (array_key_exists($type, $colorLoot) && $colorLoot[$type] == -1) {
            $colorLoot[$type] = $playerId;
            $game->setColorLoot($colorLoot);
        }

        $this->manager->flush();
    }

    // change le propriétaire de la carte avantage gold
    private function changeGoldOwner($id, $playerId) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);
        $players = $this->manager->getRepository("AugustusBundle:AugustusPlayer");
        $actualPlayer = $players->findOneById($playerId);


        if ($actualPlayer->getGold() != 0) {
            if ($game->getGoldOwner() == -1) {
                $game->setGoldOwner($playerId);
            } else {
                $otherPlayer = $players->findOneById($game->getGoldOwner());

                if ($actualPlayer->getGold() >= $otherPlayer->getGold()) {
                    $game->setGoldOwner($playerId);
                }
            }
        }

        $this->manager->flush();
    }

    // change le propriétaire de la carte avantage wheat
    private function changeWheatOwner($id, $playerId) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);
        $players = $this->manager->getRepository("AugustusBundle:AugustusPlayer");
        $actualPlayer = $players->findOneById($playerId);


        if ($actualPlayer->getWheat() != 0) {
            if ($game->getWheatOwner() == -1) {
                $game->setWheatOwner($playerId);
            } else {
                $otherPlayer = $players->findOneById($game->getWheatOwner());

                if ($actualPlayer->getWheat() >= $otherPlayer->getWheat()) {
                    $game->setWheatOwner($playerId);
                }
            }
        }

        $this->manager->flush();
    }

    // retourne le gagnant de la partie
    public function getWinner($id) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);
        $participants = $game->getPlayers()->toArray();

        $winner = $participants[0];
        $best = $this->getScores($id, $winner->getId());
        array_shift($participants);
        foreach ($participants as $player) {
            $score = $this->getScores($id, $player->getId());
            if ($score > $best ||
                $score == $best && $this->playerModel->getNbOfCardColor($player->getId(), AugustusColor::SENATOR) > $this->playerModel->getNbOfCardColor($winner->getId(), AugustusColor::SENATOR)) {
                $best = $score;
                $winner = $player;
            }
        }

        return $winner;
    }

    // retourne le score du joueur
    public function getScores($id, $playerId) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);
        $players = $this->manager->getRepository("AugustusBundle:AugustusPlayer");
        $player = $players->findOneById($playerId);

        // points des recompenses
        $rewards = $player->getAdvantage();
        if ($game->getColorLoot()[AugustusColor::SENATOR] == $playerId) {
            $rewards += 2;
        }
        if ($game->getColorLoot()[AugustusColor::GREEN] == $playerId) {
            $rewards += 4;
        }
        if ($game->getColorLoot()["all"] == $playerId) {
            $rewards += 6;
        }
        if ($game->getColorLoot()[AugustusColor::PINK] == $playerId) {
            $rewards += 8;
        }
        if ($game->getColorLoot()[AugustusColor::ORANGE] == $playerId) {
            $rewards += 10;
        }
        if ($playerId == $game->getGoldOwner()) {
            $rewards += 5;
        }
        if ($playerId == $game->getWheatOwner()) {
            $rewards += 5;
        }

        // points direct des objectifs
        $obj = 0;
        $cardPower = array();
        foreach ($player->getCtrlCards() as $card) {
            $obj += $card->getPoints();
            if ($card->getPoints() == 0) {
                array_push($cardPower, $card);
            }
        }

        // points des pouvoirs des objectifs
        $power = 0;
        foreach ($cardPower as $card) {
            switch($card->getPower()) {
                case AugustusPower::ONEPOINTBYSHIELD:
                    $pts = $this->playerModel->getNbOfToken($playerId, AugustusToken::SHIELD);
                    $power += ($pts > 8 ? 8 : $pts);
                    break;
                case AugustusPower::ONEPOINTBYDOUBLESWORD:
                    $pts = $this->playerModel->getNbOfToken($playerId, AugustusToken::DOUBLESWORD);
                    $power += ($pts > 6 ? 6 : $pts);
                    break;
                case AugustusPower::TWOPOINTBYCHARIOT:
                    $pts = $this->playerModel->getNbOfToken($playerId, AugustusToken::CHARIOT) * 2;
                    $power += ($pts > 10 ? 10 : $pts);
                    break;
                case AugustusPower::THREEPOINTBYCATAPULT:
                    $pts = $this->playerModel->getNbOfToken($playerId, AugustusToken::CATAPULT) * 3;
                    $power += ($pts > 12 ? 12 : $pts);
                    break;
                case AugustusPower::THREEPOINTBYTEACHES:
                    $pts = $this->playerModel->getNbOfToken($playerId, AugustusToken::TEACHES) * 3;
                    $power += ($pts > 15 ? 15 : $pts);
                    break;
                case AugustusPower::FOURPOINTBYKNIFE:
                    $pts = $this->playerModel->getNbOfToken($playerId, AugustusToken::KNIFE) * 4;
                    $power += ($pts > 20 ? 20 : $pts);
                    break;
                case AugustusPower::TWOPOINTBYGREENCARD:
                    $power += $this->playerModel->getNbOfCardColor($playerId, AugustusColor::GREEN) * 2;
                    break;
                case AugustusPower::TWOPOINTBYSENATORCARD:
                    $power += $this->playerModel->getNbOfCardColor($playerId, AugustusColor::SENATOR) * 2;
                    break;
                case AugustusPower::FOURPOINTBYPINKCARD:
                    $power += $this->playerModel->getNbOfCardColor($playerId, AugustusColor::PINK) * 4;
                    break;
                case AugustusPower::FIVEPOINTBYREDCARD:
                    $power += $this->playerModel->getNbOfRedPower($playerId) * 5;
                    break;
                case AugustusPower::SIXPOINTBYORANGECARD:
                    $power += $this->playerModel->getNbOfCardColor($playerId, AugustusColor::ORANGE) * 6;
                    break;
            }
        }

        return $rewards + $obj + $power;
    }

    // met isLock a true ou false pour les joueurs
    private function lockThem($id) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);

        $state = $game->getState();

        if ($state == "aveCesar" || $state == "takeLoot"
            || $state == "twoLegionOnDoubleSword" || $state == "twoLegionOnTeaches" ||
            $state == "twoLegionOnShield" || $state == "twoLegionOnKnife" ||
            $state == "twoLegionOnChariot" || $state == "twoLegionOnCatapult" ||
            $state == "oneLegionOnAnything" || $state == "twoLegionOnAnything"
            || $state == "oneCard" || $state == "moveLegion" || $state == "completeCard") {
            foreach ($game->getPlayers() as $player) {
                if ($player->getId() != $game->getAffectedPlayer()) {
                    $player->setIsLock(true);
                } else {
                    $player->setIsLock(false);
                }
            }
        } else if ($state == "removeOneLegion" || $state == "removeTwoLegion" ||
                    $state == "removeAllLegion") {
            foreach ($game->getPlayers() as $player) {
                if ($player->getId() != $game->getAffectedPlayer()) {
                    $player->setIsLock(false);
                } else {
                    $player->setIsLock(true);
                }
            }
        } else {
            foreach ($game->getPlayers() as $player) {
                $player->setIsLock(false);
            }
        }
    }

    // retourne si le joueur à le droit de réclamer sa récompense de capture
    public function canClaimReward($id, $playerId) {
        $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
        $game = $games->findOneById($id);
        $players = $this->manager->getRepository("AugustusBundle:AugustusPlayer");
        $player = $players->findOneById($playerId);

        if ($player->getAdvantage() != 0) {
            return false;
        }
        $advantage = (count($player->getCtrlCards()) - 1) * 2;
        foreach ($game->getPlayers() as $gamer) {
            if ($gamer->getAdvantage() == $advantage) {
                return false;
            }
        }
        return true;
    }

    private function cleanArray($tab) {
        $units = array();
        foreach ($tab as $u) {
            if ($u != null) {
                array_push($units, $u);
            }
        }
        return $units;
    }
}
