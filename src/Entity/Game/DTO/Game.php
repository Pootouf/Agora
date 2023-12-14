<?php

class Game {

    private int $gameId;

    private Array $players;

    private MainBoard $mainBoard;

    public function __construct(int $gameId, Array $players, MainBoard $mainBoard) {
        $this->gameId = $gameId;
        $this->players = $players;
        $this->mainBoard = $mainBoard;
    }

    public function getGameId(): int
    {
        return $this->gameId;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getMainBoard(): MainBoard
    {
        return $this->mainBoard;
    }

    public function setGameId(int $gameId): void
    {
        $this->gameId = $gameId;
    }

    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }

    public function setMainBoard(MainBoard $mainBoard): void
    {
        $this->mainBoard = $mainBoard;
    }

}