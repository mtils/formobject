<?php namespace FormObject\Support\Laravel\Event;

use FormObject\Event\DispatcherInterface;
use Illuminate\Events\Dispatcher AS LaravelDispatcher;

class Dispatcher implements DispatcherInterface{

    protected $dispatcher;

    public function __construct(LaravelDispatcher $dispatcher){
        $this->dispatcher = $dispatcher;
    }

    public function fire($event, $payload = array(), $halt = false){
        return $this->dispatcher->fire($event, $payload, $halt);
    }
}