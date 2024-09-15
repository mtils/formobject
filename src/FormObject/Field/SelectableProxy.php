<?php namespace FormObject\Field;

use \Collection\Map\ValueProxy;

class SelectableProxy extends ValueProxy{

    protected $_selected = FALSE;

    /**
     * @brief The Field holding this "Item"
     * @var Selectable
     */
    protected $_field;

    public function getField(){
        return $this->_field;
    }

    public function _setField(Selectable $field){
        $this->_field = $field;
        return $this;
    }

    public function isSelected(){
        return $this->_field->isItemSelected($this);
    }

    public function _setSelected($selected){
        $this->_selected = $selected;
    }

    public function __get($name) : mixed
    {
        if($name == 'selected'){
            return $this->isSelected();
        }
        return parent::__get($name);
    }

}