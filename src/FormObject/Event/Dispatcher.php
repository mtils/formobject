<?php namespace FormObject\Event;

class Dispatcher implements DispatcherInterface{

    /**
     * @brief Fires an event. This is exactly the laravel Event\Dispatcher
     *        Interface. If you need an adapter, write one
     */
    public function fire($event, $payload = array(), $halt = false){
        
    }

}