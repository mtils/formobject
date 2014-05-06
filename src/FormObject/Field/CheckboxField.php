<?php namespace FormObject\Field;

use FormObject\Field;
use FormObject\Attributes;

class CheckboxField extends BooleanField{

    protected function initAttributes(Attributes $attributes){
        $attributes['value'] = '1';
        $attributes['type'] = 'checkbox';
        $this->setValue($this->value);
    }

    public function getValue(){
        return $this->value;
    }

    public function setValue($value){

        $this->value = (bool)$value;

        if($this->value){
            $this->attributes->set('checked','checked');
        }
        else{
            try{
                $this->attributes->offsetUnset('checked');
            }
            catch(\OutOfBoundsException $e){
                // Do nothing
            }
        }
        return $this;
    }
    public function setFromRequest($value){
        $this->setValue($value);
    }
}