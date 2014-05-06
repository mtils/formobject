<?php

namespace FormObject\Field;

use FormObject\Attributes;

class Action extends \FormObject\Field{

    protected function initAttributes(Attributes $attributes){
        $this->setValue($this->action);
    }

    public function getAction(){
        return $this->value;
    }

    public function setAction($action){
        return $this->setValue($action);
    }
}