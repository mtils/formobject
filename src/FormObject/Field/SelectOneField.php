<?php namespace FormObject\Field;

use Collection\Iterator\CastableIterator;
use Collection\Map\Extractor;
use FormObject\Field;
use FormObject\Attributes;

class SelectOneField extends Field implements Selectable{

    protected $src;

    protected $manualExtractor;

    protected $grouper;

    protected $columns;

    public function __construct($name=NULL, $title=NULL){
        parent::__construct($name, $title);

        $this->manualExtractor = new Extractor(Extractor::KEY, Extractor::VALUE);
    }

    public function getColumns(){
        return $this->columns;
    }

    public function setColumns($columns){
        $this->columns = $columns;
        return $this->columns;
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
        $this->value = $value;
        return $this;
    }

    public function isItemSelected(SelectableProxy $item){

        if ($item->getKey() === '' && $this->value === null) {
            return true;
        }

        if ($item->getKey() === '0' && $this->value === null) {
            return false;
        }

        if ($item->getKey() === 0 && $this->value === null) {
            return false;
        }

        if ($item->getKey() === 0 && $this->value === '') {
            return false;
        }

        return ($item->getKey() == $this->value);
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

    public function getIterator(){
        return SelectableHelper::createIterator($this->getSrc(),
                                                $this,
                                                $this->manualExtractor);
    }

    public function isMultiple(){
        return FALSE;
    }

    public function copy($prefix=''){
        $copy = parent::copy($prefix);
        if($this->columns){
            $copy->setColumns($this->columns);
        }
        if($this->grouper){
            $copy->setGrouper($this->grouper);
        }
        $copy->setSrc($this->src, $this->manualExtractor);

        return $copy;
    }

}
