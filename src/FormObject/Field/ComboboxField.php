<?php namespace FormObject\Field;

use Collection\Iterator\CastableIterator;
use Collection\Map\Extractor;
use FormObject\Field;

class ComboboxField extends Field implements Selectable{

    protected $src;

    protected $manualExtractor;

    public function __construct($name=NULL, $title=NULL){
        parent::__construct($name, $title);

        $this->manualExtractor = new Extractor(Extractor::KEY, Extractor::VALUE);
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

    public function getIterator(){
        return SelectableHelper::createIterator($this->getSrc(),
                                                $this,
                                                $this->manualExtractor);
    }

    public function isMultiple(){
        return FALSE;
    }

}
