<?php


namespace AGORA\Game\SplendorBundle\Service;


use AGORA\Game\GameBundle\Entity\Game;
use AGORA\Game\SplendorBundle\Entity\SplendorCard;
use AGORA\Game\SplendorBundle\Entity\SplendorGame;
use AGORA\Game\SplendorBundle\Entity\SplendorPlayer;
use Doctrine\ORM\EntityManager;
use AGORA\PlatformBundle\Entity\Leaderboard;

class SplendorService {

    protected $manager;
    protected $gameService;
    private $nbTurn;
    private $end;
    private $winner;
    private $gameInfo;

    public function __construct(EntityManager $em, $gs) {
        $this->manager = $em;
        $this->gameService = $gs;
        $this->nbTurn = 1;
        $this->end = false;
        $this->winner = null;
        $this->gameInfo = $this->manager->getRepository('AGORAPlatformBundle:GameInfo')
                ->findOneBy(array('gameCode' => "spldr"));
    }

    public function getRandomCard($gameId, $level) {
        $x = 1; $y = 40;
        switch ($level) {
            case 2: $x = 41; $y = 70;
            break;
            case 3: $x = 71; $y = 90;
            break;
            default: break;
        }
        $id = random_int($x, $y);
        if ($gameId >= 0 && $gameId != null) {
            $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame');
            $boardCards = $game->find($gameId)->getCardsId();
            $allPlayers = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer');
            $players = $allPlayers->findBy(array('gameId' => $gameId));
            $playersCards = [];
            foreach ($players as $p) {
                $playersCards = array_merge($playersCards, $p->getBuyedCards(), $p->getReservedCards());
            }
            $cards = array_merge($playersCards, $boardCards);
            while (in_array($id, $cards)) {
                $id = rand($x, $y);
            }
        }
        return $id;
    }

    public function getTwelveRandomCards() {
        $cards = [];
        $x = 1; $y = 40;
        for ($k = 0; $k < 12; $k++) {
            if ($k >= 4) {
                $x = 41; $y = 70;
            }
            if ($k >= 8) {
                $x = 71; $y = 90;
            }
            do {
                $id = rand($x, $y);
            } while (in_array($id, $cards));
            array_push($cards, $id);
        }
        return $cards;
    }

    public function getRandomNobles($nb) {
        $ids = [];
        for ($k = 0; $k < $nb; $k++) {
            do {
                $id = rand(91, 100);
            } while (in_array($id, $ids));
            array_push($ids, $id);
        }
        return $ids;
    }

    public function createGame($name, $playersNb, $user) {
        $spldrGame = new SplendorGame();
        $cardsId = $this->getTwelveRandomCards();
        $spldrGame->setCardsId(implode(",", $cardsId));
        $nobles = $this->getRandomNobles($playersNb + 1);
        $spldrGame->setNoblesId(implode(",", $nobles));

        switch ($playersNb) {
            case 4: $spldrGame->setTokensList("7,7,7,7,7,5");
            break;
            case 3: $spldrGame->setTokensList("5,5,5,5,5,5");
            break;
            case 2: $spldrGame->setTokensList("4,4,4,4,4,5");
            break;
        }
        $spldrGame->setUserTurnId($user->getId());
        $this->manager->persist($spldrGame);
        $this->manager->flush();

        $game = new Game();
        $game->setGameId($spldrGame->getId());
        $game->setGameInfoId($this->gameInfo);
        $game->setGameName($name);
        $game->setPlayersNb($playersNb);
        $game->setHostId($user);
        $game->setState("waiting");
        $game->setCreationDate(new \DateTime("now"));
		$game->setCurrentPlayer($user->getId());
        $this->manager->persist($game);
        $this->manager->flush();

        return $game->getGameId();
    }

    public function initLeaderboard($user) {
        if ($this->manager->getRepository('AGORAPlatformBundle:Leaderboard')
                ->findOneBy(array('userId' => $user->getId(), 'gameInfoId' => $this->gameInfo)) == null) {
            $lb = new Leaderboard;
            $lb -> setGameInfoId($this->gameInfo);
            $lb -> setUserId($user);
            $lb -> setElo(2000);
            $lb -> setVictoryNb(0);
            $lb -> setLoseNb(0);
            $lb -> setEqualityNb(0);

            $this->manager->persist($lb);
            $this->manager->flush();
        }
    }

    public function createPlayer($gameId, $user) {
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $user->getId()));

        if ($player == null) {
            $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);

            $spldrPlayer = new SplendorPlayer();
            $spldrPlayer->setGameId($game);
            $spldrPlayer->setUserId($user);
            $spldrPlayer->setPrestige(0);
            $spldrPlayer->setTokensList("0,0,0,0,0,0");
            $spldrPlayer->setBuyedCards("");
            $spldrPlayer->setReservedCards("");
            $spldrPlayer->setHiddenCards("");
            $this->manager->persist($spldrPlayer);
            $this->manager->flush();

            $this->initLeaderboard($user);

            return $spldrPlayer->getId();
        }
        return -1;
    }

    public function getAllPlayers($gameId) {
        $this->manager->flush();
        $players = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findBy(array('gameId' => $gameId));
        return $players;
    }

    public function getPlayers($gameId) {
        return $this->getAllPlayers($gameId);
    }

    public function getGame($gameId) {
        $this->manager->flush();
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        return $game;
    }

    public function getPlayerFromUser($user, $gameId) {
        return $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $user));
    }

    public function setLastMovePlayedForPlayer($userId, $gameId) {
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $player->setLastMovePlayed(new \DateTime("now"));
        $this->manager->persist($player);
        $this->manager->flush();
    }

    /**
     * Fonction permettant de quitter la partie identifiée par $gameId si elle est non commencée.
     */
    public function quitGame($user, $gameId) {
        $player = $this->getPlayerFromUser($user, $gameId);
        if ($player != null) {
            $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
                ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
            $playersNb = count($this->getPlayers($gameId));
            
            if ($playersNb == 1 || $player->getUserId()->getId() == $game->getHostId()->getId()) {
                $this->supressGame($gameId);
            } elseif (1 < $playersNb && $playersNb < $game->getPlayersNb()) {
                $this->manager->remove($player);
                $this->manager->flush($player);
            } else {
                return;
            }
        }
    }

    public function supressGame($gameId) {
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        $players = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')->findBy(array('gameId' => $gameId));
        foreach ($players as $player) {
            $this->manager->remove($player);
            $this->manager->flush($player);
        }

        $g = $this->manager->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $this->manager->remove($g);
        $this->manager->flush($g);

        $this->manager->remove($game);
        $this->manager->flush($game);
    }

    public function reserveCard($gameId, $userId, $cardId) {
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        if ($game->getUserTurnId() != $userId) {
            return null;
        }
        $cards = $game->getCardsId();
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $i = array_search($cardId, $cards);
        $playerCard = $player->getReservedCards();
        //Si la carte est sur le plateau et que le joueur a moins de 3 cartes deja reservées
        if (is_numeric($i) && count($playerCard) < 4) {
            //On calcul le niveau de la carte à piocher
            $level = (1 + intval($i / 4));
            //On pioche la carte qui va remplacer la carte reservée
            $newCard = $this->getRandomCard($gameId, $level);
            //On met la carte piochée à la place de celle réservée
            $cards[$i] = $newCard;
            $game->setCardsId(implode(",", $cards));
            $gameTokens = $game->getTokensList();
            $gold = 0;
            //Si il reste des jetons or(joker) sur le plateau
            if ($gameTokens[5] > 0) {
                //Le joueur en prend un
                $tokens = $player->getTokensList();
                $tokens[5] += 1;
                $gameTokens[5] -= 1;
                $game->setTokensList(implode(",", $gameTokens));
                $player->setTokensList(implode(",", $tokens));
                $gold = 1;
            }

            $this->manager->persist($game);
            $this->manager->flush();

            //On ajoute la carte réservée dans la main du joueur
            array_push($playerCard, $cardId);
            $player->setReservedCards(implode(",", $playerCard));

            $this->manager->persist($player);
            $this->manager->flush();

            return array($newCard, $gold);
        }

        if (count($playerCard) < 4) {
            $gold = 0;
            $gameTokens = $game->getTokensList();
            //Si il reste des jetons or(joker) sur le plateau
            if ($gameTokens[5] > 0) {
                //Le joueur en prend un
                $tokens = $player->getTokensList();
                $tokens[5] += 1;
                $gameTokens[5] -= 1;
                $game->setTokensList(implode(",", $gameTokens));
                $player->setTokensList(implode(",", $tokens));
                $gold = 1;
                $this->manager->persist($game);
                $this->manager->flush();
            }
            //On ajoute la carte réservée dans la main du joueur
            array_push($playerCard, $cardId);
            $player->setReservedCards(implode(",", $playerCard));
            $this->manager->persist($player);
            $this->manager->flush();
            $newCard = 0;
            return array($newCard, $gold);
        }

        return null;
    }

    public function buyCard($gameId, $userId, $cardId) {
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        if ($game->getUserTurnId() != $userId) {
            return null;
        }
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $cardsGame = $game->getCardsId();
        $cardsReserved = $player->getReservedCards();
        $i = array_search($cardId, $cardsGame);
        $j = array_search($cardId, $cardsReserved);
        $playerCard = $player->getBuyedCards();
        $cardTable = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorCard')->find($cardId);
        $playerTokens = $player->getTokensList();
        $gameTokens = $game->getTokensList();

        $bonus = [0,0,0,0,0];
        //On calcul les bonus du joueur
        foreach ($player->getBuyedCards() as $id) {
            if ($id != 0) {
                $buyedCard = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorCard')->find($id);
                switch ($buyedCard->getBonus()) {
                    case "Green":
                        $bonus[0] += 1;
                        break;
                    case "Blue":
                        $bonus[1] += 1;
                        break;
                    case "Red":
                        $bonus[2] += 1;
                        break;
                    case "White":
                        $bonus[3] += 1;
                        break;
                    case "Black":
                        $bonus[4] += 1;
                        break;
                }
            }
        }

        $jokerNeed = 0;
        //On verifie si le joueur a les ressources necessaires
        for ($k = 0; $k < 5; $k++) {
            $jok = 0;
            $tok = $cardTable->getTokens($k);
            if ($tok > $playerTokens[$k] + $bonus[$k]) {
                $jokerNeed += ($tok - ($playerTokens[$k] + $bonus[$k]));
                $jok = ($tok - ($playerTokens[$k] + $bonus[$k]));
            }
            //else {
            $reste = $tok - $bonus[$k] - $jok;
            if ($bonus[$k] >= $tok) {
                $reste = 0;
            }
            //On en profite pour mettre à jour les ressources du joueur
            $playerTokens[$k] = $playerTokens[$k] - $reste;
            //Et pour mettre a jour les ressources du plateau
            $gameTokens[$k] = $gameTokens[$k] + $reste;
            //}
        }

        //Si la carte est sur le plateau ou dans les carte réservé du joueur
        // et que le joueur a les ressources necessaires
        if ((is_numeric($i) || is_numeric($j)) && $jokerNeed <= $playerTokens[5]) {
            $newCard = null;
            //Si la carte est sur le plateau
            if (is_numeric($i)) {
                //On calcul le niveau de la carte à piocher
                $level = (1 + intval($i / 4));
                //On pioche la carte qui va remplacer la carte achetée
                $newCard = $this->getRandomCard($gameId, $level);
                //On met la carte piochée à la place de celle achetée
                $cardsGame[$i] = $newCard;
                $game->setCardsId(implode(",", $cardsGame));
            } else {
                //Si la carte est dans les cartes réservées du joueur
                //On la retire des cartes réservées
                array_splice($cardsReserved, $j, 1);
                $player->setReservedCards(implode(",", $cardsReserved));
            }



            //On ajoute la carte achetée dans la main du joueur
            array_push($playerCard, $cardId);
            $player->setBuyedCards(implode(",", $playerCard));
            //On retire les ressources joker necessaire pour l'achat
            $playerTokens[5] = $playerTokens[5] - $jokerNeed;
            //Et on les ajoute au plateau
            $gameTokens[5] = $gameTokens[5] + $jokerNeed;
            $player->setTokensList(implode(",", $playerTokens));
            //On ajoute le prestige de la carte au joueur
            $prestige = $player->getPrestige() + $cardTable->getPrestige();
            $player->setPrestige($prestige);
            $this->manager->persist($player);
            $this->manager->flush();

            //On met la table splendor_game à jour
            $game->setTokensList(implode(",", $gameTokens));
            $this->manager->persist($game);
            $this->manager->flush();

            return array($newCard, implode(",", $playerTokens), $prestige, implode(",", $gameTokens));
        }

        return null;
    }

    public function canBuyCard($gameId, $userId, $cardId) {

        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $cardsGame = $game->getCardsId();
        $cardsReserved = $player->getReservedCards();
        $i = array_search($cardId, $cardsGame);
        $j = array_search($cardId, $cardsReserved);
        $playerCard = $player->getBuyedCards();
        $cardTable = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorCard')->find($cardId);
        $playerTokens = $player->getTokensList();
        $gameTokens = $game->getTokensList();

        $bonus = [0,0,0,0,0];
        //On calcule les bonus du joueur
        foreach ($player->getBuyedCards() as $id) {
            if ($id != 0) {
                $buyedCard = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorCard')->find($id);
                switch ($buyedCard->getBonus()) {
                    case "Green":
                        $bonus[0] += 1;
                        break;
                    case "Blue":
                        $bonus[1] += 1;
                        break;
                    case "Red":
                        $bonus[2] += 1;
                        break;
                    case "White":
                        $bonus[3] += 1;
                        break;
                    case "Black":
                        $bonus[4] += 1;
                        break;
                }
            }
        }

        $jokerNeed = 0;
        //On verifie si le joueur a les ressources necessaires
        for ($k = 0; $k < 5; $k++) {
            $jok = 0;
            $tok = $cardTable->getTokens($k);
            if ($tok > $playerTokens[$k] + $bonus[$k]) {
                $jokerNeed += ($tok - ($playerTokens[$k] + $bonus[$k]));
                $jok = ($tok - ($playerTokens[$k] + $bonus[$k]));
            }
            //else {
            $reste = $tok - $bonus[$k] - $jok;
            if ($bonus[$k] >= $tok) {
                $reste = 0;
            }
            //On en profite pour mettre à jour les ressources du joueur
            $playerTokens[$k] = $playerTokens[$k] - $reste;
            //Et pour mettre a jour les ressources du plateau
            $gameTokens[$k] = $gameTokens[$k] + $reste;
            //}
        }


        //Si la carte est sur le plateau ou dans les carte réservé du joueur
        // et que le joueur a les ressources necessaires
        return (is_numeric($i) || is_numeric($j)) && $jokerNeed <= $playerTokens[5];
    }

    public function print_txt($txt) {
        print($txt);
    }

    public function getTokens($gameId, $userId, $tokens) {
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        if ($game->getUserTurnId() != $userId) {
            return null;
        }
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $gameTokens = $game->getTokensList();
        $playerTokens = $player->getTokensList();
        $nbOne = 0;
        $nbTwo = 0;
        //On verifie que le joueur a le droit de prendre ces jetons
        for ($k = 0; $k < 5; $k++) {
            //Si il n'a pas le droit on return
            if ($tokens[$k] > $gameTokens[$k] || $tokens[$k] > 2 || ($tokens[$k] == 2 && $gameTokens[$k] < 4)
                || ($tokens[$k] == 2 && ($nbTwo + $nbOne) != 0) || ($tokens[$k] == 1 && ($nbTwo != 0 || $nbOne > 2))) {
                return null;
            }
            if ($tokens[$k] == 1) {
                $nbOne++;
            }
            if ($tokens[$k] == 2) {
                $nbTwo++;
            }
            //Sinon on lui donne les jetons
            $playerTokens[$k] += $tokens[$k];
            //Et on les retire du plateau
            $gameTokens[$k] -= $tokens[$k];
        }

        $game->setTokensList(implode(",", $gameTokens));
        $this->manager->persist($game);
        $this->manager->flush();

        $player->setTokensList(implode(",", $playerTokens));
        $this->manager->persist($player);
        $this->manager->flush();

        return array(implode(",", $playerTokens), implode(",", $gameTokens));
    }


    //Retourne les ids des cartes nobles qui peuvent visiter le joueur sous forme de tableau
    public function canVisitNoble($gameId, $userId) {
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        if ($game->getUserTurnId() != $userId) {
            return null;
        }
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $nobles = $game->getNoblesId();
        $bonus = [0,0,0,0,0];
        //On calcul les bonus du joueur
        foreach ($player->getBuyedCards() as $id) {
            if ($id != 0) {
                $buyedCard = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorCard')->find($id);
                switch ($buyedCard->getBonus()) {
                    case "Green":
                        $bonus[0] += 1;
                        break;
                    case "Blue":
                        $bonus[1] += 1;
                        break;
                    case "Red":
                        $bonus[2] += 1;
                        break;
                    case "White":
                        $bonus[3] += 1;
                        break;
                    case "Black":
                        $bonus[4] += 1;
                        break;
                }
            }
        }
        $result = [];
        //Pour chaque noble sur le plateau
        foreach ($nobles as $id) {
            $cardNoble = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorCard')->find($id);
            //Si le joueur a les bonus necessaires alors on ajoute le noble dans le tableau retourné
            if ($bonus[0] >= $cardNoble->getEmeraldTokens() && $bonus[1] >= $cardNoble->getSapphireTokens()
                && $bonus[2] >= $cardNoble->getRubyTokens() && $bonus[3] >= $cardNoble->getDiamondTokens()
                && $bonus[4] >= $cardNoble->getOnyxTokens()) {
                array_push($result, $id);
            }
        }
        return $result;
    }

    public function visitNoble($gameId, $userId, $idNoble) {
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        if ($game->getUserTurnId() != $userId) {
            return null;
        }
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $gameNobles = $game->getNoblesId();
        //On retire la carte noble du plateau
        $k = array_search($idNoble, $gameNobles);
        array_splice($gameNobles, $k, 1);
        $game->setNoblesId(implode(",", $gameNobles));
        $this->manager->persist($game);
        $this->manager->flush();
        //On recupere la carte Noble correspondant à l'id
        $cardNoble = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorCard')->find($idNoble);
        //On ajoute le prestige de la carte Noble au prestige du joueur
        $prestige = $player->getPrestige() + $cardNoble->getPrestige();
        $player->setPrestige($prestige);
        $this->manager->persist($player);
        $this->manager->flush();
        return $prestige;
    }

    public function endTurn($gameId, $userId) {
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);

        if ($game->getUserTurnId() != $userId) {
            return null;
        }
        $players = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findBy(array('gameId' => $gameId));
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        //On calcul la somme des jetons du joueur
        $total = 0;
        foreach ($player->getTokensList() as $token) {
            $total += intval($token);
        }
        //Si le joueur a plus de 10 jetons on retourne False
        if ($total > 10) {
            return array(false, implode(",", $player->getTokensList()), $total);
        }

        //Sinon on cherche quel est le prochain joueur
        for ($k = 0; $k < count($players); $k++) {
            if ($players[$k]->getUserId()->getId() == $userId) {
                break;
            }
        }
        $newPlayer = (($k + 1) % count($players) == 0 ? $players[($k - (count($players) - 1))] : $players[$k + 1]);
        //Et on change le tour du joueur
        $game->setUserTurnId($newPlayer->getUserId()->getId());
        $this->manager->persist($game);
        $this->manager->flush();
        $splendorGame = $this->manager->getRepository('AGORAGameGameBundle:Game')->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $splendorGame->setCurrentPlayer($newPlayer->getUserId()->getId());
        $this->manager->persist($splendorGame);
        $this->manager->flush();

        if ($player->getPrestige() >= 15) {
            $this->end = true;
        }

        if ($this->end && ($this->nbTurn % count($players)) == 0) {
            //FIN DE LA PARTIE
            $g = $this->manager->getRepository('AGORAGameGameBundle:Game')->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
            if ($g->getstate() != "finished") {
                // Calcul du classement
                $players = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
                    ->findBy(array('gameId' => $gameId), array('prestige' => 'DESC', 'buyedCards' => 'ASC'));
                $finalWinner = $players[0];

                $this->gameService->computeELO($players, $gameId, $this->gameInfo, $finalWinner);
                $this->winner = $this->manager->getRepository('AGORAUserBundle:User')->find($finalWinner->getUserId());

                $g->setState("finished");
                $this->manager->persist($g);
                $this->manager->flush();
                $this->supressGame($gameId);
            }

            return array(true, $this->winner->getUsername());
        }

        $this->nbTurn += 1;
        return array(false, $newPlayer->getUserId()->getId());
    }

    public function removeTokens($gameId, $userId, $tokens) {
        $game = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorGame')->find($gameId);
        if ($game->getUserTurnId() != $userId) {
            return null;
        }
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $tokensPlayer = $player->getTokensList();
        $tokensGame = $game->getTokensList();

        for ($k = 0; $k < count($tokensPlayer); $k++) {
            // Le nombre de pierres pour une sorte change seulement si le joueur en redonne
            $diff = $tokensPlayer[$k] - $tokens[$k];
            if ($diff > 0) {
                $tokensGame[$k] += ($diff);
            }
            $tokensPlayer[$k] = $tokens[$k];
        }
        $player->setTokensList(implode(",", $tokensPlayer));
        $this->manager->persist($player);
        $this->manager->flush();

        $game->setTokensList(implode(",", $tokensGame));
        $this->manager->persist($game);
        $this->manager->flush();

        return implode(",", $tokensPlayer);
    }

    public function  addHiddenCard($gameId, $userId, $cardId) {
        $player = $this->manager->getRepository('AGORAGameSplendorBundle:SplendorPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        $hide = $player->getHiddenCards();
        array_push($hide, $cardId);
        $player->setHiddenCards(implode(",", $hide));
        $this->manager->persist($player);
        $this->manager->flush();
    }
}
