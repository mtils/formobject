<?php namespace FormObject\Validator;

use FormObject\Form;

interface FactoryInterface{
    public function createValidator(Form $form);
}