<?php

class ListOfCards extends Component {

    private Array $cardsId;

    public function __construct(int $id, Array $cardsId) {
        parent::__construct($id);
        $this->cardsId = $cardsId;
    }

    public function getCardsId(): Array {
        return $this->cardsId;
    }

    public function setCardsId(Array $cardsId): void {
        $this->cardsId = $cardsId;
    }
}