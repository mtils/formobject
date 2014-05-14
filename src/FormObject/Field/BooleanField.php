<?php namespace FormObject\Field;

use FormObject\Field;

class BooleanField extends Field{

    public $mustBeTrue = False;

    public function getValue(){
        return (bool)$this->value;
    }

    public function getMustBeTrue(){
        return $this->mustBeTrue;
    }

    public function setMustBeTrue($mustBeTrue){
        $this->mustBeTrue = $mustBeTrue;
        return $this;
    }
}