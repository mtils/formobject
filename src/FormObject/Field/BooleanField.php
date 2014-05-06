<?php namespace FormObject\Field;

use FormObject\Field;

class BooleanField extends Field{

    public $mustBeTrue = False;

    public function getValue(){
        return (bool)$this->value;
    }
}