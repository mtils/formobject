<?php namespace FormObject\Field;

use \BadMethodCallException;
use FormObject\Field;
use FormObject\Attributes;

class PasswordField extends TextField{

    public $minLength = NULL;
    public $maxLength = NULL;
    public $htmlAllowed = NULL;
    public $multiLine = FALSE;

    protected function initAttributes(Attributes $attributes){
        parent::initAttributes($attributes);
        $attributes['type'] = 'password';
    }

    public function setMultiLine($multiLine){
        if($multiLine){
            throw new BadMethodCallException('Password Fields cant be multiline');
        }
        $this->multiLine = $multiLine;
        return $this;
    }
}