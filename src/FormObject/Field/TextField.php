<?php

namespace FormObject\Field;
use FormObject\Field;

class TextField extends Field{

    public $minLength = NULL;
    public $maxLength = NULL;
    public $allowHtml = NULL;
    public $multiLine = FALSE;

    public function createAttributes(){
        $attributes = parent::createAttributes();
        $attributes['value'] = $this->value;
        return $attributes;
    }

    public function getValue(){
        return (string)$this->value;
    }
}