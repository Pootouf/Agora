<?php

class Player{
    private int $playerId;

    private PersonalBoard $board;

    public function __construct(int $playerId, int $boardId){
        $this->playerId = $playerId;
        $this->board = new PersonalBoard($this, $boardId);
    }
}