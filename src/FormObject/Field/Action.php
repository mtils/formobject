<?php

namespace FormObject\Field;

use FormObject\Attributes;

class Action extends \FormObject\Field{

    protected $_isSelected = FALSE;

    protected function initAttributes_(Attributes $attributes){
//         $this->setName($this->action);
    }

    public function getAction(){
        if($this->form){
            return $this->form->getName() . '_' . parent::getName();
        }
    }

    public function setName($name){
        $cleanedName = str_replace('action_','',$name);
        $this->value = $cleanedName;
        return parent::setName("action_$cleanedName");
    }

    public function setValue($value){
        return $this;
    }

    public function isSelected(){
        return $this->_isSelected;
    }

    public function setSelected($selected){
        $this->_isSelected = $selected;
        return $this;
    }
}