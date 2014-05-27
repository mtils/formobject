<?php namespace FormObject\Support\Laravel;

use FormObject\AdapterFactoryInterface;
use FormObject\Form;

class AdapterFactoryLaravel implements AdapterFactoryInterface{

    protected $renderer;

    protected $redirector;

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

}