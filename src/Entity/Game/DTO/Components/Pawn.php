<?php

class Pawn extends Component {

    private String $color;
    private Player $player;

    public function __construct(int $id, String $color, Player $player) {
        parent::__construct($id);
        $this->color = $color;
        $this->player = $player;
    }

    public function getColor(): String {
        return $this->color;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function setColor(String $color): void { 
        $this->color = $color;
    }

    public function setPlayer(Player $player): void {
        $this->player = $player;
    }
}