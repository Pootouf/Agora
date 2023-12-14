<?php

abstract class Component {

    // ATTRIBUTES
    private int $id;
    private int $helpId;

    public function __construct(int $id) {
        $this->id = $id;
    }

    // GETTER

    public function getHelpId():int {
        return $this-> helpId;
    }

    public function getId():int{
        return $this -> id;
    }

    // SETTER

    public function setId(int $id):void {
        $this -> id = $id;
    }

    public function setHelpId(int $helpId):void {
        $this -> helpId = $helpId;
    }
}