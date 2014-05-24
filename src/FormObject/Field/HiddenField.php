<?php

namespace FormObject\Field;

use FormObject\Attributes;

class HiddenField extends \FormObject\Field{
    protected function updateAttributes(Attributes $attributes){
        parent::updateAttributes($attributes);
        $attributes->set('type','hidden');
        $attributes->set('value',$this->value);
    }
}