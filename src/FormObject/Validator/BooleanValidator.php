<?php namespace FormObject\Validator;

use FormObject\Registry;
use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionException;

class BooleanValidator extends Validator{

    protected static $msgTemplates = array('mustBeTrue'=>'Please confirm');

    public $mustBeTrue = FALSE;

    public function isValid($value){
        if($this->mustBeTrue){
            if(is_bool($value) && !$value){
                $this->addMessage('mustBeTrue');
                return FALSE;
            }
        }
        return TRUE;
    }

    public function isRequired(){
        return $this->required;
    }
}