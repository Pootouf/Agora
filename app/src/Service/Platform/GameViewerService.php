<?php

namespace App\Service\Platform;


class GameViewerService
{
    private array $routesArray;

    public function __construct()
    {
        $this->routesArray = [
            "6QP" => "app_game_show_sixqp",
            "SPL" => "app_game_show_spl",
            "GLM" => "app_game_show_glm",
            "MYR" => "app_game_show_myr"
        ];
    }



    //return the route of the game with its label and id
    public function getGameViewRouteFromLabel(string $label){
        return $this->routesArray[$label];
    }
}