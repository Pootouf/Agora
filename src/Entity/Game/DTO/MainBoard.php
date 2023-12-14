<?php

class MainBoard extends Board {

    private int $gameId;
    

    public function __construct(int $gameId, int $boardId) {
        parent::__construct($boardId);
        $this->gameId = $gameId;
    }

    public function getGameId(): int {
        return $this->gameId;
    }
}

?>