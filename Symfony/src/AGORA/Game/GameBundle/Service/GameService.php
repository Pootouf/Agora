<?php

namespace AGORA\Game\GameBundle\Service;

use Doctrine\ORM\EntityManager;
use AGORA\PlatformBundle\Entity\Leaderboard;

class GameService {

    protected $manager;

    public function __construct(EntityManager $em) {
        $this->manager = $em;
    }

    /**
     * Calcul le coefficient K en fonction de l'elo donnée en paramètre.
     * Un coefficient est défini de manière arbitraire en fonction de l'ELO actuelle.
     * Plus l'ELO est grande, plus le coeff est petit.
     */
    private function coefficient($elo) {
        if ($elo < 2000) {
            $k = 80;
        } else if ($elo < 4000) {
            $k = 50;
        } else if ($elo < 4800) {
            $k = 30;
        } else {
            $k = 20;
        }
        return $k;
    }

    /**
     * Calcul l'estimation p(D) pour le joueur 1.
     */
    private function estimation($player1Elo, $player2Elo) {
        return 1 / (1 + pow(10, (($player2Elo - $player1Elo) / 400)));
    }
    
    /**
     * Fonctions permettant de gérer le classement du joueur à la suite de la terminaison de la partie.
     * /!\ À n'appeler qu'à la fin de la partie.
     * Formule :
     *      En+1 = En + K * (W - p(D)) le nouvel Elo où
     *      En = ancien Elo
     *      K = coefficient
     *      W = résultat de la partie : 1 -> victoire, 0.5 -> égalité et 0 -> défaite
     *      p(D) = résultat attendu, estimation
     */
    public function computeELO($players, $gameId, $gameInfo, $winner) {
        $lb = $this->manager->getRepository('AGORAPlatformBundle:Leaderboard');

        $equality = false;
        if ($winner == null) {
            $equality = true;
        }

        // Modification de l'ELO
        foreach ($players as $player) {
            $playerLB = $lb->findOneBy(array('userId' => $player->getUserId(), 'gameInfoId' => $gameInfo));
            $playerElo = $playerLB->getElo();

            if ($equality) {
                $playerLB->setEqualityNb($playerLB->getEqualityNb() + 1);
            } else {
                if($player == $winner) {
                    $playerLB->setVictoryNb($playerLB->getVictoryNb() + 1);
                } else {
                    $playerLB->setLoseNb($playerLB->getLoseNb() + 1);
                } 
            }

            $k = $this->coefficient($playerElo);

            foreach ($players as $p) {
                if ($equality) {
                    $w = 0.5;
                } else {
                    if($player == $winner) {
                        $w = 1;
                    } else {
                        $w = 0;
                    } 
                }

                if ($p != $player) {
                    $plb = $lb->findOneBy(array('userId' => $p->getUserId(), 'gameInfoId' => $gameInfo));
                    $esti = $this->estimation($playerElo, $plb->getElo());
                    $playerElo = $playerElo + $k * ($w - $esti);
                }
            }
            $playerLB->setELO($playerElo);

            // Enregistrement des modifications pour le joueur
            $this->manager->persist($playerLB);
            $this->manager->flush();
        }
    }
}
