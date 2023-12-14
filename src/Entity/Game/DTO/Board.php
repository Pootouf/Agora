<?php

abstract class Board {

    private int $BoardId;

    public function __construct(int $BoardId){
        $this->BoardId = $BoardId;
    }

    public function getBoardId(): int {
        return $this->BoardId;
    }

}