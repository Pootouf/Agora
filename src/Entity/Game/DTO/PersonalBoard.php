<?php

class PersonalBoard extends Board {
    private Player $player;

    public function __construct(Player $player, int $boardId) {
        parent::__construct($boardId);
        $this->player = $player;
    }
}