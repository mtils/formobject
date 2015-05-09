<?php namespace FormObject\Field;

use Collection\Iterator\CastableIterator;
use Collection\Map\Extractor;
use Collection\ColumnList;
use FormObject\Field;
use FormObject\Attributes;
use OutOfBoundsException;
use FormObject\Form;
use IteratorAggregate;
use Iterator;
use ArrayAccess;
use BadMethodCallException;
use DomainException;

class EditManyField extends Field implements Iterator, ArrayAccess{

    protected $src;

    protected $manualExtractor;

    protected $columns;

    protected $itemForm;

    protected $iteratorPosition = 0;

    protected $currentForm;

    protected $itemForms;

    protected $addItemTitle = 'Hinzufügen...';

    protected $removeItemTitle = 'Löschen';

    public function getItemForm(){
        return $this->itemForm;
    }

    public function setItemForm(Form $form){
        $this->itemForm = $form;
        return $this;
    }

    public function getFields(){
        return $this->itemForm->fields;
    }

    public function isValid(){

        if($this->valid === NULL){
            $valid = TRUE;
            foreach($this as $itemForm){
                if(!$itemForm->isValid()){
                    $valid = FALSE;
                    break;
                }
            }
            $this->valid = $valid;
        }

        return $this->valid;
    }

    public function getColumns(){
        return $this->columns;
    }

    public function setColumns($columns){
        $this->columns = $columns;
        return $this;
    }

    public function updateAttributes(Attributes $attributes){

        parent::updateAttributes($attributes);

        try{
            unset($attributes['value']);
        }
        catch(OutOfBoundsException $e){

        }

    }

    public function setValue($value){
        if(!$value){
            $this->value = array();
        }
        else{
            $this->value = $value;
        }
        return $this;
    }

    public function rewind(){
        $this->iteratorPosition = 0;
    }

    public function current(){
        return $this->createForm($this->iteratorPosition);
    }

    public function key(){
        return $this->iteratorPosition;
    }

    public function valid(){
        return isset($this->value[$this->iteratorPosition]);
    }

    public function next(){
        $this->currentForm = NULL;
        $this->iteratorPosition++;
    }

    public function isMultiple(){
        return TRUE;
    }

    public function getAttributeTitles()
    {
        $titles = [$this->getName()=>$this->getTitle()];

        foreach($this as $idx=>$itemForm) {
            foreach ($itemForm->dataFields as $dataField) {
                $titles = array_merge($titles, $dataField->getAttributeTitles());
            }
        }
        return $titles;
    }

    public function formTemplate()
    {
        return $this->createForm('x');
    }

    protected function createForm($idx){

        if(!$this->itemForm){
            throw new DomainException("Assign a itemForm via setItemForm before using EditManyField");
        }

        $itemForm = $this->itemForm->copy();

        $itemForm->setName($this->getName()."_{$idx}");

        $values = array();

        foreach($this->itemForm->dataFields as $field){

            $fieldName = $field->getName();

            if($this->columns && !in_array($fieldName, $this->columns)){
                continue;
            }

            $copy = $field->copy();
            $newFieldName = $this->name . "[$idx][$fieldName]";

            $copy->setName($newFieldName);

            if(isset($this->value[$idx]) && isset($this->value[$idx][$fieldName])){
                $values[$newFieldName] = $this->value[$idx][$fieldName];
            }

            $itemForm->fields->push($copy);
        }

        if($form = $this->getForm()){
            $itemForm->setValidationBroker($form->getValidationBroker());
            if($form->wasSubmitted()){
                $itemForm->fakeSubmit();
                $itemForm->fillByRequestArray($values);
            }
            else{
                $itemForm->fillByArray($values);
            }
        }

        return $itemForm;

    }

    protected function getOrCreateForm($idx){
        if(!isset($this->itemForms[$idx])){
            if(!isset($this->value[$idx])){
                throw new OutOfBoundsException("No data for key $idx");
            }
            $this->itemForms[$idx] = $this->createForm($idx);

        }
        return $this->itemForms[$idx];
    }

    public function offsetGet($offset){
        if(is_numeric($offset)){
            return $this->getOrCreateForm($offset);
        }
    }

    public function offsetSet($offset, $value){
        
    }

    public function offsetUnset($offset){
        
    }

    public function offsetExists($offset){
        
    }

    public function getAddItemTitle()
    {
        return $this->addItemTitle;
    }

    public function setAddItemTitle($title)
    {
        $this->addItemTitle = $title;
        return $this;
    }

    public function getRemoveItemTitle()
    {
        return $this->removeItemTitle;
    }

    public function setRemoveItemTitle($title)
    {
        $this->removeItemTitle = $title;
        return $this;
    }

    public function __toString_()
    {
        $res = parent::__toString();
        die('Karpotten');
    }
}
