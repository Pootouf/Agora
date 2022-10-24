<?php

namespace AGORA\Game\AzulBundle\Model;

/**
 * Cette classe sert d'enum pour les différents type de tuiles
 * dans le jeu
 */
abstract class TuileType 
{
    const none = 0;
    const orange = 1;
    const red = 2;
    const black = 3;
    const cyan = 4;
    const blue = 5;
}
?>