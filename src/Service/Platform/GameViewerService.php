<?php

namespace App\Service\Platform;

use Doctrine\Common\Collections\ArrayCollection;

class GameViewerService
{
    private array $routesArray;

    public function __construct()
    {
        $this->routesArray = [
            "6QP" =>"/game/sixqp"
        ];
    }



    //return the route of the game with its label and id
    public function getGameViewRouteFromId(int $id, string $label){
        return $this->routesArray[$label]."/".$id;
    }
}