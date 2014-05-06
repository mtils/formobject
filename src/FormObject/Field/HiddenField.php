<?php

namespace FormObject\Field;

use FormObject\Attributes;

class HiddenField extends \FormObject\Field{
    protected function initAttributes(Attributes $attributes){
        $attributes->set('type','hidden');
        $attributes->set('value',$this->value);
    }
    public function createAttributes(){
        $attributes = parent::createAttributes();
        $attributes['value'] = $this->value;
        return $attributes;
    }
}