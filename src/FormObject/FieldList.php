<?php

namespace FormObject;

use \Countable;
use \ArrayAccess;
use \IteratorAggregate;
use \OutOfBoundsException;

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

    public function isTabBar()
    {
        return $this->hasSwitchableChildren();
    }

    public function isValid(){

        foreach( $this->getDataFields() as $field) {
            if (!$field->isValid()) {
                return false;
            }
        }

        return true;
    }

    public function hasSwitchableChildren()
    {

        foreach ($this->fieldLists() as $fieldList) {
            if ($fieldList->isSwitchable() && $fieldList->getClassName() != 'SelectOneGroup') {
                return true;
            }
        }

        return false;
    }

    public function getClassName(){
        if ($this->isTabBar()) {
            return 'SwitchableFieldList';
        }
        return parent::getClassName();
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
        if(isset($this->fields[$offset])){
            return TRUE;
        }
        if(isset($this->dataFields[$offset])){
            return TRUE;
        }
        return FALSE;
    }

    public function offsetGet($offset){
        if(isset($this->fields[$offset])){
            if($this->fields[$offset] instanceof Field &&
               !$this->fields[$offset] instanceof FieldList){
                return $this->fields[$offset]->getValue();
            }
        }
        return $this->findDataField($offset)->getValue();
    }

    public function offsetSet($offset, $value){

        if (!isset($this->fields[$offset])) {
            $this->keyOrder[] = $offset;
        }

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

    public function holdsData(){
        return FALSE;
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

        $this->offsetSet($field->getName(), $field);

        $numArgs = func_num_args();

        if($numArgs > 1){
            $args = func_get_args();
            for($i=1;$i<$numArgs;$i++){
                $this->push($args[$i]);
            }
        }
        return $this;
    }

    public function get($fieldName){
        if(isset($this->fields[$fieldName])){
            return $this->fields[$fieldName];
        }
        return $this->findDataField($fieldName);
    }

    public function __invoke($fieldName){
        return $this->get($fieldName);
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

    public function hasFieldLists()
    {
        return (bool)$this->fieldLists();
    }

    public function findDataField($name){
        foreach($this->dataFields as $field){
            if($field->getName() == $name){
                return $field;
            }
        }
        throw new OutOfBoundsException("Datafield '$name' not found");
    }

    public function getDataFields($prefix=NULL){

        if($prefix === NULL){
            return $this->dataFields;
        }

        $prefixed = [];

        foreach($this->dataFields as $field){
            if($field->getPrefix() == $prefix){
                $prefixed[] = $field;
            }
        }

        return $prefixed;

    }

    public function getDataFieldNames($prefix=NULL){
        $names = [];
        foreach($this->getDataFields($prefix) as $field){
            $names[] = $field->getPlainName();
        }
        return $names;
    }

    public function getPrefixes(){

        $prefixes = [];

        foreach($this->dataFields as $field){
            $prefix = $field->getPrefix();
            if(!in_array($prefix, $prefixes)){
                $prefixes[] = $prefix;
            }
        }

        return $prefixes;

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
            if($dataField->getId() == $field->getId()){
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

    public function before($fieldName, $ohNoIMeantAfter=FALSE){

        $lastAddedKey = array_pop($this->keyOrder);

        $newKeyOrder = array();

        foreach($this->keyOrder as $key){
            if($key == $fieldName){
                if(!$ohNoIMeantAfter){
                    $newKeyOrder[] = $lastAddedKey;
                    $newKeyOrder[] = $key;
                }
                else{
                    $newKeyOrder[] = $key;
                    $newKeyOrder[] = $lastAddedKey;
                }
            }
            else{
                $newKeyOrder[] = $key;
            }
        }

        $this->keyOrder = $newKeyOrder;

        return $this;

    }

    public function after($fieldName){

        return $this->before($fieldName, TRUE);

    }

    public function isFirstField(Field $field){
        if(isset($this->keyOrder[0])){
            if($this->keyOrder[0] == $field->getName()){
                return TRUE;
            }
        }
        return FALSE;
    }

    public function isLastField(Field $field){
        $last = (count($this->keyOrder)-1);
        if(isset($this->keyOrder[$last])){
            if($this->keyOrder[$last] == $field->getName()){
                return TRUE;
            }
        }
        return FALSE;
    }

    public function isFirstList(){
        if($this->parent){
            $fieldListCount = 0;
            foreach($this->parent as $field){
                if($field instanceof self){
                    if( $field === $this && $fieldListCount == 0){
                        return TRUE;
                    }
                $fieldListCount++;
                }
            }
        }
        return FALSE;
    }

    public function isLastList(){
        if($this->parent){
            $fieldListCount = 0;
            foreach($this->parent as $field){
                if($field instanceof self){
                   $fieldListCount++;
                }
            }
            $i=0;
            foreach($this->parent as $field){
                if($field instanceof self){
                    $i++;
                    if( $field === $this && $i == $fieldListCount){
                        return TRUE;
                    }
                }
            }
        }
        return FALSE;
    }

    public function parentIsFieldList()
    {
        return ($this->getParent() instanceof self);
    }

    public function first(){
        if(!isset($this->keyOrder[0])){
            return;
        }
        return $this->get($this->keyOrder[0]);
    }

    public function rawFields()
    {
        return $this->fields;
    }

    public function copy($prefix='')
    {

        $copy = parent::copy($prefix);

        foreach ($this as $field) {
            $copy->push($field->copy($prefix));
        }

        return $copy;

    }
}
