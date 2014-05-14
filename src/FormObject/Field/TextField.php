<?php

namespace FormObject\Field;
use FormObject\Field;
use FormObject\Attributes;

class TextField extends Field{

    public $minLength = NULL;
    public $maxLength = NULL;
    public $htmlAllowed = NULL;
    public $multiLine = FALSE;

    protected function initAttributes(Attributes $attributes){
        $attributes['type'] = 'text';
    }

    public function getValue(){
        return (string)$this->value;
    }

    public function getMinLength($length){
        return $this->minLength;
    }

    public function setMinLength($length){
        $this->minLength = $length;
        return $this;
    }

    public function getMaxLength($length){
        return $this->maxLength;
    }

    public function setMaxLength($length){
        $this->maxLength = $length;
        return $this;
    }

    public function isHtmlAllowed(){
        return $this->allowHtml;
    }

    public function getHtmlAllowed(){
        return $this->isHtmlAllowed();
    }

    public function setHtmlAllowed($allowed){
        $this->htmlAllowed = $allowed;
    }

    public function allowHtml($allowed=TRUE){
        return $this->setHtmlAllowed($allowed);
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