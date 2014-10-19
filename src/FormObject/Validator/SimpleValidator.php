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

    public function getValidator(){
        return $this;
    }

    public function setValidator($validator){
        return;
    }

    public function set($fieldName, Validator $validator){
        $this->validators[$fieldName] = $validator;
    }

    public function validate($data){
        $result = TRUE;
//         print_r($data);
        foreach($data as $fieldName=>$value){
            if(isset($this->validators[$fieldName])){
//                 echo "\nChecking $value of $fieldName";
                if(!$this->validators[$fieldName]->isValid($value)){
                    $result = $this->validationResult[$fieldName] = FALSE;
                }
                else{
                    $this->validationResult[$fieldName] = TRUE;
                }
            }
//             else{
//                 echo "\nNo validator for $fieldName";
//             }
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
            $this->validate($this->form->data);
        }
        if(isset($this->validationResult[$fieldName])){
            return !$this->validationResult[$fieldName];
        }
        return FALSE;
    }

    public function getMessages($fieldName){
        if(!$this->validated){
            $this->validate($this->form->data);
        }
        if(isset($this->validators[$fieldName])){
            return $this->validators[$fieldName]->getMessages();
        }
        return array();
    }

    public function allMessages(){

        $errors = [];

        foreach($this->validators as $fieldName=>$validator){
            $errors[$fieldName] = $validator->getMessages();
        }

        return $errors;
    }

    public function getRuleNames($fieldName){
        if(isset($this->validators[$fieldName])){
            $refl = new ReflectionClass($this->validators[$fieldName]);
            return array(str_replace('validator','',strtolower($refl->getShortName())));
        }
        return array();
    }

    /**
     * @brief Creates the exception if you like exception based validation
     *
     * @param mixed $validator
     * @return Exception
     **/
    public function createValidationException($validator){
        return new ValidationException($this->allMessages());
    }

}