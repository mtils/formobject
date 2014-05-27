<?php namespace FormObject;

use FormObject\Validator\ValidatorAdapterInterface;
use FormObject\Validator\SimpleAdapter;

class AdapterFactorySimple implements AdapterFactoryInterface{

    protected $renderer;

    protected $validatorAdapter;

    protected $redirector;

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

}