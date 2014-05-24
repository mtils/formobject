<?php namespace FormObject\Field;

use FormObject\Field;

class BooleanField extends Field{

    public function getValue(){
        return (bool)$this->value;
    }

}