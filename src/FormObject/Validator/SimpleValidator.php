<?php namespace FormObject\Validator;

use \ReflectionClass;

class SimpleValidator implements ValidatorAdapterInterface{

    protected $validators = array();

    protected $validationResult = array();

    protected $validated = FALSE;

    protected $form;

    public function __construct(\FormObject\Form $form){
        $this->form = $form;
    }

    public function set($fieldName, Validator $validator){
        $this->validators[$fieldName] = $validator;
    }

    public function validate(){
        $result = TRUE;
        foreach($this->validators as $fieldName=>$validator){
            if(!$validator->isValid($this->form[$fieldName])){
                $result = $this->validationResult[$fieldName] = FALSE;
            }
            else{
                $this->validationResult[$fieldName] = TRUE;
            }
        }
        $this->validated = TRUE;
        return $result;
    }

    public function isRequired($fieldName){
        if(isset($this->validators[$fieldName]) && $this->validators[$fieldName] instanceof RequiredValidator){
            return $this->validators[$fieldName]->required;
        }
        return FALSE;
    }


    public function hasErrors($fieldName){
        if(!$this->validated){
            $this->validate();
        }
        if(isset($this->validationResult[$fieldName])){
            return !$this->validationResult[$fieldName];
        }
        return TRUE;
    }

    public function getMessages($fieldName){
        if(!$this->validated){
            $this->validate();
        }
        if(isset($this->validators[$fieldName])){
            return $this->validators[$fieldName]->getMessages();
        }
        return array();
    }

    public function getRuleNames($fieldName){
        if(isset($this->validators[$fieldName])){
            $refl = new ReflectionClass($this->validators[$fieldName]);
            return array(str_replace('validator','',strtolower($refl->getShortName())));
        }
        return array();
    }
}