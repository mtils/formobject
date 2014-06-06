<?php namespace FormObject;

use FormObject\Validator\ValidatorAdapterInterface;
use FormObject\Validator\SimpleAdapter;

class AdapterFactorySimple implements AdapterFactoryInterface{

    protected $renderer;

    protected $validatorAdapter;

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
        $adapter = new SimpleAdapter();
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
        if(!$this->eventDispatcher){
            $this->eventDispatcher = new EventDispatcher();
        }
        return $this->eventDispatcher;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher){
        $this->dispatcher = $dispatcher;
        return $this;
    }

    public function getRequestAsArray($method){
        if($method == 'post'){
            return $_POST;
        }
        elseif($method == 'get'){
            return $_GET;
        }
        return $_REQUEST;
    }

}