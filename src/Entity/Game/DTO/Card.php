<?php

class Card extends Component {

    // ATTRIBUTES

    private int $value;

    // CONSTRUCTOR

    public function __construct(int $id, int $value) {
        parent::__construct($id);
        $this->value = $value;
    }

    // GETTER

    public function getValue(): int { 
        return $this->value;
    }

    // SETTER

    public function setValue(int $value): void {
        $this->value = $value;
    }
}