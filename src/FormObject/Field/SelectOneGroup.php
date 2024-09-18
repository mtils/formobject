<?php namespace FormObject\Field;

use FormObject\FieldList;
use Collection\Map\Extractor;
use ReturnTypeWillChange;

class SelectOneGroup extends FieldList implements Selectable{

    protected $src;

    protected $manualExtractor;

    protected $columns;

    public function __construct($name=NULL, $title=NULL){
        parent::__construct($name, $title);
        $this->setSwitchable(TRUE);

        $this->manualExtractor = new Extractor(Extractor::KEY, Extractor::VALUE);
    }

    public function getColumns(){
        return $this->columns;
    }

    public function setColumns($columns){
        $this->columns = $columns;
        return $this->columns;
    }

    public function setValue($value){
        $this->value = $value;
        return $this;
    }

    public function isItemSelected(SelectableProxy $item){
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

    #[ReturnTypeWillChange]
    public function getIterator(){
        return SelectableHelper::createIterator($this->getSrc(),
                                                $this,
                                                $this->manualExtractor);
    }

    public function getGroup($optionKey){
        $idx = NULL;
        $i = 0;
        foreach($this as $option){
            if($option->key == $optionKey){
                $idx = $i;
                break;
            }
            $i++;
        }
        if( $idx !== NULL && isset($this->keyOrder[$idx])){
            return $this->fields[$this->keyOrder[$idx]];
        }
    }

    public function isMultiple(){
        return FALSE;
    }

    public function holdsData(){
        return TRUE;
    }

    public function copy($prefix=''){
        $copy = static::create($this->name, $this->title);
        foreach($this as $field){
            $copy->push($field->copy($prefix));
        }
        return $copy;
    }

}