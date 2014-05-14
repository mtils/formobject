<?php

namespace FormObject\FieldList;
use FormObject\FieldList;

class Iterator implements \Iterator{

    protected $fieldList;
    protected $position = 0;
    protected $fieldCount = 0;

    public function __construct(FieldList $fieldList){
        $this->fieldList = $fieldList;
        $this->fieldCount = count($fieldList);
    }

    public function current(){

        $keys = $this->fieldList->getKeyOrder();
        $key = $keys[$this->position];

        return $this->fieldList->get($key);
    }

    public function key(){
        return $this->position;
    }

    public function next(){
        ++$this->position;
    }

    public function rewind(){
        $this->position = 0;
    }

    public function valid(){
        return ($this->position < $this->fieldCount);
    }
}