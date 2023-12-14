<?php
 
class Token extends Component {

    private String $type;

    public function __construct(int $id, String $type) {
        parent::__construct($id);
        $this->type = $type;
    }

    public function getType():String {
        return $this->type;
    }

    public function setType(String $type):void {
        $this->type = $type;
    }
}
