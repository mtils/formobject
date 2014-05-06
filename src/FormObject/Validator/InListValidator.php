<?php

namespace FormObject\Validator;

class InListValidator extends RequiredValidator{

    public $allowedValues = array();

    protected static $msgTemplates = array('inList'=>'This value is not in the list of allowed values');

    public function isValid($value){

        $value = (string)$value;
        $valid = TRUE;

        if(trim($value) != ''){
            if(count($this->allowedValues)){
                $valid = FALSE;
                foreach($this->allowedValues as $allowed){
                    // cast both to string
                    if("$value" == "$allowed"){
                        $valid = TRUE;
                    }
                }
                if(!$valid){
                    $this->addMessage('inList');
                }
            }
        }

        return (parent::isValid($value) && $valid);
    }

    public static function containsHtml($string){
        return ($string != strip_tags($string));
    }
}