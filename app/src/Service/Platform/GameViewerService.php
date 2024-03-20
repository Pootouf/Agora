<?php

namespace App\Service\Platform;


class GameViewerService
{
    private array $routesArray;

    public function __construct()
    {
        $this->routesArray = [
            "6QP" => "app_game_show_sixqp"
        ];
    }



    //return the route of the game with its label and id
    public function getGameViewRouteFromLabel(string $label){
        return $this->routesArray[$label];
    }
}