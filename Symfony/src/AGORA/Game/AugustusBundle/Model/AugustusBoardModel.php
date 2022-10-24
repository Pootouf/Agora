<?php

namespace AGORA\Game\AugustusBundle\Model;

use Doctrine\ORM\EntityManager;
use AGORA\Game\AugustusBundle\Entity\AugustusToken;

class AugustusBoardModel {
  
  protected $manager;
  
  //On construit notre api avec un entity manager permettant l'accès à la base de données
  public function __construct(EntityManager $em) {
    $this->manager = $em;
  }

  // fillLine : Place des cartes sur le terrain, si il en manque.
  public function fillLine($idBoard) {
    //03/02/2022 : rajouter conditions si deck vide ?
    // Récupération du board avec son id.
    $boards = $this->manager->getRepository('AugustusBundle:AugustusBoard');
    $board = $boards->findOneById($idBoard);
    $deck = $board->getDeck()->toArray();
    $card = array_pop($deck);
    //$board->removeCardFromDeck($card); // ligne rajouté le 03/02/2022
    $i = count($board->getObjLine());
    while ($i < 5) {
      if (!$card->getIsInLine()) {
        $card->setIsInLine(true);
        $this->manager->flush();
        $i = $i + 1;
      }
      $card = array_pop($deck);
      //$board->removeCardFromDeck($card); // ligne rajouté le 03/02/2022
    }
    $this->manager->flush();
  }
    
  // resetBag : supprime les tokens du sac, puis ajoute tous les tokens dans le sac.
  public function resetBag($idBoard) {
    
    // Récupération du board avec son id.
    $boards = $this->manager->getRepository('AugustusBundle:AugustusBoard');
    $board = $boards->findOneById($idBoard);
    
    // On vide le sac.
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
      $nbAlea1 = rand_int(0, 22);
      $nbAlea2 = rand_int(0, 22);
      $transiToken1 = $tokenBag->get($nbAlea1);
      $transiToken2 = $tokenBag->get($nbAlea2);
      $tokenBag->remove($nbAlea1);
      $tokenBag->remove($nbAlea1);
      $tokenBag->set($nbAlea2, $transiToken1);
      $tokenBag->set($nbAlea1, $transiToken2);
    }

    $board->setTokenBag($tokenBag->toArray());
  }
  
  // takeToken : Prend le dernier token du sac (le sac est déjà mélangé).
  // return : Le dernier token du sac.
  public function takeToken($idBoard) {
    
    // Récupération du board avec son id.
    $boards = $this->manager->getRepository('AugustusBundle:AugustusBoard');
    $board = $boards->findOneById($idBoard);
    
    $bag = $board->getTokenBag();
    $token = array_pop($bag);
    $board->setTokenBag($bag);
    $this->manager->flush();
    return $token;
  }
  
  // TakeCard : prend la dernière carte du deck (le deck est déjà mélangé).
  // return : La dernière carte du deck.
  public function takeCard($idBoard) {
    
    // Récupération du board avec son id.
    $boards = $this->manager->getRepository('AugustusBundle:AugustusBoard');
    $board = $boards->findOneById($idBoard);
    $deck = $board->getDeck()->toArray();
    $card = array_pop($deck);
    while ($card->getIsInLine() || $card->getPlayer() != null) { // 01/03/2022 : ajout d'une conditions pour les carte tirées  du centre
      $card = array_pop($deck);
    }
    $board->removeCardFromDeck($card); // la première carte face caché
    //$card->setBoard(null);
    $this->manager->persist($card);
    $this->manager->flush();
    $this->fillLine($idBoard);
    $this->manager->flush();
    return $card;
  }

  public function takeCardFromCenter($idBoard, $idCard) { 
    $boards = $this->manager->getRepository('AugustusBundle:AugustusBoard');
    $board = $boards->findOneById($idBoard);

    $cards = $this->manager->getRepository('AugustusBundle:AugustusCard');
    $card = $cards->findOneById($idCard);
	 
    $board->removeCardFromDeck($card);
    $card->setIsInLine(false);
    //$card->setBoard(null);
    $this->manager->flush();
    $this->fillLine($idBoard);
    $this->manager->flush();
    return $card;
  }
}
