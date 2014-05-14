<?php

namespace FormObject\Validator;

class MultipleInListValidator extends RequiredValidator{

    public $allowedValues = array();

    protected static $msgTemplates = array('inList'=>'This value is not in the list of allowed values');

    public function isValid($value){
        if(!is_array($value)){
            $value = array((string)$value);
        }
        $valid = TRUE;

        if($this->required && !count($value)){
            $this->addMessage('required');
            return FALSE;
        }

        if(count($this->allowedValues)){
            $valid = TRUE;
            $disallowedValues = array();
            foreach($value as $idx=>$selected){
                var_dump($idx); var_dump($selected);
                $selectedIsValid = FALSE;
                foreach($this->allowedValues as $allowed){
                    // cast both to string
                    if("$selected" == "$allowed"){
                        $selectedIsValid = TRUE;
                        break;
                    }
                }
                if($selectedIsValid == FALSE){
                    echo " HIA:";
                    $disallowedValues[] = $selected;
                    $valid = FALSE;
                }
            }
            if(!$valid){
                $this->addMessage('inList');
            }
        }

        return $valid;
    }

    public static function containsHtml($string){
        return ($string != strip_tags($string));
    }
}