<?php namespace FormObject\Field;

use Collection\Map\ProxyExtractor;

class SelectableExtractor extends ProxyExtractor{

    protected $_field = NULL;

    protected function createProxy($item, $key, $value, $position){
        $proxy = new SelectableProxy($item);
        $proxy->_setKey($key);
        $proxy->_setValue($value);
        $proxy->_setPosition($position);
        $proxy->_setField($this->_field);
        return $proxy;
    }

    public function getField(){
        return $this->_field;
    }

    public function _setField(Selectable $field){
        $this->_field = $field;
        return $this;
    }
}
