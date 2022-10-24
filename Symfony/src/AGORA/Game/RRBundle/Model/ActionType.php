<?php

namespace AGORA\Game\RRBundle\Model;
/**
 * Cette classe corespont au différent type d'action que le joueur peut effectué.
 */
abstract class ActionType{
    const railConstruction = 0;
    const trainsFactories = 1;
    const industry = 2;
    const auxiliaries = 3;
    const industryAndRailConstruction = 4;
}
?>