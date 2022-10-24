<?php

namespace AGORA\Game\RRBundle\Model;

/**
 * Cette classe sert d'enum pour les différents type de rail
 * dans le jeu
 */
abstract class RailType 
{
    const none = 0;
    const black = 1;
    const grey = 2;
    const brown = 3;
    const beige = 4;
    const white = 5;
}
?>