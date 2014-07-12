<?php namespace FormObject\Field;

use InvalidArgumentException;
use IteratorAggregate;
use ArrayIterator;

class OptionGrouper implements IteratorAggregate{

    protected $groupGetter;

    protected $titles = array();

    protected $selectField;

    protected $iteratorPos = 0;

    protected $currentGroupTitle = '';

    protected $currentItem;

    public function __construct($groupGetter=NULL){
        if($groupGetter){
            $this->setGroupGetter($groupGetter);
        }
    }

    public function getGroupGetter(){
        return $this->groupGetter;
    }

    public function setGroupGetter($getter){
        if(!is_callable($getter)){
            throw new InvalidArgumentException('Groupgetter has to be callable');
        }
        $this->groupGetter = $getter;
        return $this;
    }

    public function getTitle($groupName){
        if(isset($this->titles[$groupName])){
            return $this->titles[$groupName];
        }
        return $groupName;
    }

    public function setTitle($groupName, $title){
        $this->titles[$groupName] = $title;
        return $this;
    }

    public function removeTitle($groupName){
        if(isset($this->titles[$groupName])){
            unset($this->titles[$groupName]);
        }
        return $this;
    }

    public function getGrouped(){
        return $this;
    }

    public function getSelectField(){
        return $this->selectField;
    }

    public function setSelectField(Selectable $field){
        $this->selectField = $field;
        return $this;
    }

    protected function buildGroupArray(){
        $groupedArray = array();
        $getter = $this->groupGetter;

        foreach($this->selectField as $option){
            $group = $getter($this, $option->getSrc());
            $title = $this->getTitle($group);
            if(!isset($groupedArray[$title])){
                $groupedArray[$title] = array();
            }
            $groupedArray[$title][] = $option;
        }
        return $groupedArray;
    }

    public function getIterator(){
        return new ArrayIterator($this->buildGroupArray());
    }

}