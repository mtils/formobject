<?php namespace FormObject\Field;

use FormObject\Field;
use FormObject\Attributes;

class CheckboxField extends BooleanField{

    protected function updateAttributes(Attributes $attributes){
        parent::updateAttributes($attributes);
        $attributes['value'] = '1';
        $attributes['type'] = 'checkbox';
        if($this->value){
            $attributes->set('checked','checked');
        }
        else{
            try{
                $attributes->offsetUnset('checked');
            }
            catch(\OutOfBoundsException $e){
                // Do nothing
            }
        }
    }

    public function getValue(){
        return $this->value;
    }

    public function setValue($value){
        $this->value = (bool)$value;
        return $this;
    }
    public function setFromRequest($value){
        $this->setValue($value);
    }
}