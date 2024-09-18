<?php

namespace FormObject\FieldList;
use FormObject\FieldList;
use ReturnTypeWillChange;

class Iterator implements \Iterator{

    protected $fieldList;
    protected $position = 0;
    protected $fieldCount = 0;

    public function __construct(FieldList $fieldList){
        $this->fieldList = $fieldList;
        $this->fieldCount = count($fieldList);
    }

    #[ReturnTypeWillChange] public function current(){

        $keys = $this->fieldList->getKeyOrder();
        $key = $keys[$this->position];

        return $this->fieldList->get($key);
    }

    #[ReturnTypeWillChange] public function key(){
        return $this->position;
    }

    #[ReturnTypeWillChange] public function next(){
        ++$this->position;
    }

    #[ReturnTypeWillChange] public function rewind(){
        $this->position = 0;
    }

    #[ReturnTypeWillChange] public function valid(){
        return ($this->position < $this->fieldCount);
    }
}