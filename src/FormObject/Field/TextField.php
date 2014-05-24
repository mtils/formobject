<?php

namespace FormObject\Field;
use FormObject\Field;
use FormObject\Attributes;

class TextField extends Field{

    public $multiLine = FALSE;

    protected function updateAttributes(Attributes $attributes){
        parent::updateAttributes($attributes);
        $attributes['type'] = 'text';
    }

    public function getValue(){
        return (string)$this->value;
    }

    public function isMultiLine(){
        return $this->multiLine;
    }

    public function getMultiLine(){
        return $this->isMultiLine();
    }

    public function setMultiLine($multiLine){
        $this->multiLine = $multiLine;
        return $this;
    }
}
