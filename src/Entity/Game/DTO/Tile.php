<?php

class Tile extends Component {

    private String $type;

    public function __construct(int $id, String $type) {
        parent::__construct($id);
        $this->type = $type;
    }

    public function getType() :String {
        return $this->type;
    }

    public function setTypename(String $name) : void {
        $this->type = $name;
    }

}