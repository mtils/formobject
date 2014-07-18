<?php namespace FormObject\Field;

use Collection\Map\ProxyExtractor;
use Collection\Map\ValueProxy;

class SelectableExtractor extends ProxyExtractor{

    protected $_field = NULL;

    protected function createProxy($item, $key, $value, $position){
        
        return new SelectableProxy($item);
        return $proxy;
    }

    protected function setProxyValues(ValueProxy &$proxy, $key, $value, $position){
        parent::setProxyValues($proxy, $key, $value, $position);
        $proxy->_setField($this->_field);
    }

    public function getField(){
        return $this->_field;
    }

    public function _setField(Selectable $field){
        $this->_field = $field;
        return $this;
    }
}
