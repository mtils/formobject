<?php

namespace FormObject\Validator;

class TextValidator extends RequiredValidator{

    public $minLength = -1;
    public $maxLength = -1;
    public $allowHtml = FALSE;
    public $regexp = '';

    protected static $msgTemplates = array('minLength'=>'This field needs at least {minLength} characters',
                                           'maxLength'=>'This field can contain {maxLength} chars at max',
                                           'noHtml'=>'Html isnt allowed here',
                                           'regexp'=>'The string doesnt match {regexp}');

    public function isValid($value){

        $value = (string)$value;
        $valid = TRUE;

        if(trim($value) != ''){
            if($this->minLength != -1){
                if(strlen($value) < $this->minLength){
                    $this->addMessage('minLength');
                    $valid = FALSE;
                }
            }

            if($this->maxLength != -1){
                if(strlen($value) > $this->maxLength){
                    $valid = FALSE;
                    $this->addMessage('maxLength');
                }
            }

            if($this->regexp){
                $status = @preg_match($this->regexp, $value);
                if($status === FALSE){
                    $this->addMessage('regexp');
                    $valid = FALSE;
                }
            }

            if(!$this->allowHtml && self::containsHtml($value)){
                $this->addMessage('noHtml');
                $valid = FALSE;
            }
        }

        return (parent::isValid($value) && $valid);
    }

    public static function containsHtml($string){
        return ($string != strip_tags($string));
    }
}