<?php

abstract class Board {

    private int $BoardId;

    public function getBoardId(): int {
        return $this->BoardId;
    }

}