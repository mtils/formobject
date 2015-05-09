<?php namespace FormObject\Validation;


use FormObject\Form;

interface BrokerFactoryInterface
{

    /**
     * Create a validation broker object for $form
     *
     * @param \FormObject\Form $form
     * @return \FormObject\Validation\BrokerInterface
     **/
    public function createBroker(Form $form);

}