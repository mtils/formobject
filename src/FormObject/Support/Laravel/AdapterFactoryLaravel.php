<?php namespace FormObject\Support\Laravel;

use FormObject\AdapterFactoryInterface;
use FormObject\Form;
use FormObject\EventDispatcherInterface;
use \Input;

class AdapterFactoryLaravel implements AdapterFactoryInterface{

    protected $renderer;

    protected $redirector;

    protected $eventDispatcher;

    public function getRenderer(){
        return $this->renderer;
    }

    public function setRenderer($renderer){
        $this->renderer = $renderer;
        return $this;
    }

    public function createValidatorAdapter(Form $form, $validator){
        $adapter = new ValidatorAdapter($form, $validator);
        $adapter->setValidator($validator);
        return $adapter;
    }

    public function getRedirector(){
        return $this->redirector;
    }

    public function setRedirector($redirector){
        $this->redirector = $redirector;
        return $this;
    }

    public function getEventDispatcher(){
        return $this->eventDispatcher;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher){
        $this->eventDispatcher = $dispatcher;
        return $this;
    }

    public function getRequestAsArray($method){
        if($old = Input::old()){
            $data = $old;
        }
        else{
            $data = Input::all();
        }
        return $data;
    }

}