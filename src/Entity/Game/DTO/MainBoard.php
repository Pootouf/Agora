<?php

class MainBoard extends Board {

    private int $gameId;
    

    public function __construct(int $gameId) {
        $this->gameId = $gameId;
    }

    public function getGameId(): int {
        return $this->gameId;
    }
}

?>