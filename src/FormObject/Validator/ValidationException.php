<?php namespace FormObject\Validator;

use RuntimeException;

class ValidationException extends RuntimeException{

    protected $errors;

    public function __construct($errors, $msg='', $code=NULL){
        parent::__construct($msg, $code);
        $this->errors = $errors;
    }

    public function getErrors(){
        return $this->errors;
    }

}