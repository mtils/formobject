<?php namespace FormObject\Validator;

use FormObject\Registry;
use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionException;

class RequiredValidator extends Validator{

    protected static $msgTemplates = array('required'=>'This field is required');

    public $required = FALSE;

    protected $castToEmpty = '';

    public function isValid($value){
        if($this->required){
            if(!$value){
                $this->addMessage('required');
                return FALSE;
            }
        }
        return TRUE;
    }

    public function isRequired(){
        return $this->required;
    }
}