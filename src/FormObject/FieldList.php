<?php

namespace FormObject;

use \Countable;
use \ArrayAccess;
use \IteratorAggregate;

class FieldList extends Field implements Countable, ArrayAccess, IteratorAggregate{

    protected $fields = array();

    protected $keyOrder = array();

    protected $dataFields = array();

    protected $switchable = FALSE;

    protected $form;

    protected $parent;

    public function getForm(){
        return $this->form;
    }

    public function isSwitchable(){
        return $this->switchable;
    }

    public function getSwitchable(){
        return $this->switchable;
    }

    public function setSwitchable($switchable){
        $this->switchable = $switchable;
        return $this;
    }

    public function setForm(Form $form){
        $this->form = $form;
        foreach($this->fields as $field){
            $field->setForm($this->form);
        }
        return $this;
    }

    public function getKeyOrder(){
        return $this->keyOrder;
    }

    public function offsetExists($offset){
        return isset($this->fields[$offset]);
    }

    public function offsetGet($offset){
        return $this->fields[$offset];
    }

    public function offsetSet($offset, $value){

        $this->keyOrder[] = $offset;
        $this->fields[$offset] = $value;

        if($value->holdsData()){
            $this->_addDataField($value);
        }
        $value->setName($offset);
        $value->setParent($this);
        if($this->form){
            $value->setForm($this->form);
        }
    }

    public function offsetUnset($offset){
        $idx = array_search($offset, $this->keyOrder);
        unset($this->keyOrder[$idx]);
        $this->keyOrder = array_values($this->keyOrder);
        unset($this->fields[$offset]);
    }

    public function getIterator(){
        return new FieldList\Iterator($this);
    }

    /**
    * @brief Adds a Field or Fields to the FieldList
    * 
    * @return FieldList
    */
    public function push(Field $field){
        $this->offsetSet($field->getName(),$field);
        return $this;
    }

    public function count(){
        return count($this->fields);
    }

    public function fieldLists(){
        $lists = array();
        foreach($this as $field){
            if($field instanceof self){
                $lists[] = $field;
            }
        }
        return $lists;
    }

    public function getDataFields(){
        return $this->dataFields;
    }

    public function getParent(){
        return $this->parent;
    }

    public function setParent($parent){
        $this->parent = $parent;
        if($parent instanceof self){
            foreach($this->dataFields as $field){
                $parent->_addDataField($field);
            }
        }
    }

    public function _addDataField(Field $field){
        $this->dataFields[] = $field;
        if($this->parent instanceof self){
            $this->parent->_addDataField($field);
        }
    }

    public function _removeDataField(Field $field){
        $removeIdx = -1;
        foreach($this->dataFields as $i=>$dataField){
            if($dataField->getId() == $field->getId){
                $removeIdx = $i;
                break;
            }
        }
        if($removeIdx != -1){
            unset($this->dataFields[$removeIdx]);
            # preserve indexes
            $this->dataFields = array_values($this->dataFields);
        }
        if($this->parent instanceof self){
            $this->parent->_removeDataField($field);
        }
    }
}