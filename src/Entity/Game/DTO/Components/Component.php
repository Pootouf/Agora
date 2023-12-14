<?php

abstract class Component {

    // ATTRIBUTES
    private int $id;
    private \App\Entity\Game\Help $help;

    public function __construct(int $id) {
        $this->id = $id;
    }

    // GETTER

    public function getId():int{
        return $this -> id;
    }

    public function getHelp(): \App\Entity\Game\Help
    {
        return $this->help;
    }

    // SETTER

    public function setId(int $id):void {
        $this -> id = $id;
    }

    public function setHelp(\App\Entity\Game\Help $help): void
    {
        $this->help = $help;
    }
}