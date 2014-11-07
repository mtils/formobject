<?php namespace FormObject\Http;

use FormObject\Form;

class ActionUrlProviderChain implements ActionUrlProviderInterface{

    protected $providers = [];

    /**
     * This method returns a default action where to send the form if none was
     * excplicit setted via Form::setAction()
     *
     * @param \FormObject\Form $form
     * @return string
     **/
    public function setActionUrl(Form $form){
        if($provider = $this->findProvider($form)){
            return $provider->setActionUrl($form);
        }
    }

    /**
     * This method returns a true if this provider is sure to be the right one
     * assign an action to the form
     *
     * @param \FormObject\Form $form
     * @return bool
     **/
    public function matches(Form $form){
        return ($this->findProvider($form) instanceof ActionUrlProviderInterface);
    }

    protected function findProvider(Form $form){
        foreach(array_reverse($this->providers) as $provider){
            if($provider->matches($form)){
                return $provider;
            }
        }
    }

    public function add(ActionUrlProviderInterface $provider){
        $this->providers[] = $provider;
    }

}