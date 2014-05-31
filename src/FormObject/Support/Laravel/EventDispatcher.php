<?php namespace FormObject\Support\Laravel;

use FormObject\EventDispatcherInterface;
use Illuminate\Events\Dispatcher;

class EventDispatcher implements EventDispatcherInterface{

    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher){
        $this->dispatcher = $dispatcher;
    }

    public function fire($event, $payload = array(), $halt = false){
        return $this->dispatcher->fire($event, $payload, $halt);
    }
}