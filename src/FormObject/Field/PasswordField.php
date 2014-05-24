<?php namespace FormObject\Field;

use \BadMethodCallException;
use FormObject\Field;
use FormObject\Attributes;

class PasswordField extends TextField{

    public $multiLine = FALSE;

    protected function updateAttributes(Attributes $attributes){
        parent::updateAttributes($attributes);
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