<?php namespace FormObject;


interface AdapterFactoryInterface{

    public function getDefaultAction(Form $form);

    public function getRenderer();

    public function createValidatorAdapter(Form $form, $validator);

    public function getEventDispatcher();

    public function getRequestAsArray($method);

}