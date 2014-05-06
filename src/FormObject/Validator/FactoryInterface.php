<?php namespace FormObject\Validator;

use \FormObject\FormItem;

interface FactoryInterface{
    public function createForField(FormItem $item);
}