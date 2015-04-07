<?php namespace FormObject\Support\Laravel\Validator;

use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Support\MessageBag;
use RuntimeException;

class ValidationException extends RuntimeException implements MessageProvider{

    protected $messages;

    public function __construct(MessageBag $messages, $msg='', $code=NULL){
        parent::__construct($msg, $code);
        $this->messages = $messages;
    }

    public function getMessageBag(){
        return $this->messages;
    }

}