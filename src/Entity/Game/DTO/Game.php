<?php

class Game {

    private int $gameId;

    public function __construct(int $gameId) {
        $this->gameId = $gameId;
    }

    public function getGameId(): int
    {
        return $this->gameId;
    }

    public function setGameId(int $gameId): void
    {
        $this->gameId = $gameId;
    }

}