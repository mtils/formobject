<?php namespace FormObject\Support\Laravel;

use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;
use RuntimeException;

class ValidationException extends RuntimeException implements MessageProviderInterface{

    protected $messages;

    public function __construct(MessageBag $messages, $msg='', $code=NULL){
        parent::__construct($msg, $code);
        $this->messages = $messages;
    }

    public function getMessageBag(){
        return $this->messages;
    }

}