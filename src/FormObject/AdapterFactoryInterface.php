<?php namespace FormObject;


interface AdapterFactoryInterface{

    public function getRenderer();

    public function createValidatorAdapter(Form $form, $validator);

    public function getEventDispatcher();

    public function getRequestAsArray($method);

}