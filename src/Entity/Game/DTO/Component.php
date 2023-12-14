<?php

abstract class Component {

    // ATTRIBUTES
    private int $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    // GETTER

    // public Help getHelp() {
    //     return $this-> 
    // }

    public function getId():int{
        return $this -> id;
    }

    // SETTER

    public function setId(int $id):void {
        $this -> id = $id;
    }
}