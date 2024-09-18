<?php namespace FormObject\Field;

use Collection\Iterator\CastableIterator;
use Collection\Map\Extractor;
use Collection\ColumnList;
use FormObject\Field;
use FormObject\Attributes;
use OutOfBoundsException;
use ReturnTypeWillChange;

class SelectManyField extends Field implements Selectable{

    protected $src;

    protected $manualExtractor;

    protected $columns;

    protected $grouper;

    public function __construct($name=NULL, $title=NULL){
        parent::__construct($name, $title);

        $this->manualExtractor = new Extractor(Extractor::KEY, Extractor::VALUE);
    }

    public function getColumns(){
        return $this->columns;
    }

    public function setColumns($columns){
        if($columns instanceof ColumnList){
            $this->columns = $columns;
        }
        else{
            $this->columns = ColumnList::fromArray($columns);
        }
        return $this;
    }

    public function getGrouper(){
        return $this->grouper;
    }

    public function setGrouper($grouper){
        $this->grouper = $grouper;
        $this->grouper->setSelectField($this);
        return $this;
    }

    public function hasGroups(){
        return ($this->grouper instanceof OptionGrouper);
    }

    public function getGrouped(){
        return $this->grouper->getGrouped();
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

    public function isItemSelected(SelectableProxy $item){

        if(!$this->value){
            return FALSE;
        }

        foreach($this->value as $key){
            if($item->getKey() == $key){
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getSrc(){
        if(!$this->src){
            return array();
        }
        return $this->src;
    }

    public function setSrc($src, $extractor=NULL){
        $this->src = $src;
        if(!is_null($extractor)){
            $this->manualExtractor = $extractor;
        }
        return $this;
    }

    #[ReturnTypeWillChange]
    public function getIterator(){
        return SelectableHelper::createIterator($this->getSrc(),
                                                $this,
                                                $this->manualExtractor);
    }

    public function isMultiple(){
        return TRUE;
    }

    public function copy($prefix=''){
        $copy = parent::copy($prefix);
        if($this->columns){
            $copy->setColumns($this->columns);
        }
        $copy->setSrc($this->getSrc(), $this->manualExtractor);
        return $copy;
    }

}
