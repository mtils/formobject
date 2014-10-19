<?php namespace FormObject\Field;

use Collection\Iterator\CastableIterator;
use Collection\Map\Extractor;
use Collection\ColumnList;
use FormObject\Field;
use FormObject\Attributes;
use OutOfBoundsException;

class SelectFlagsField extends SelectManyField{

    protected $stateArray = [];

    public function __construct($name=NULL, $title=NULL){
        parent::__construct($name, $title);

        $this->manualExtractor = new Extractor(Extractor::KEY, Extractor::VALUE);
        $this->value = 0;

    }

    public function getClassName(){
        return 'MultiCheckboxField';
    }

    public function updateAttributes(Attributes $attributes){
        parent::updateAttributes($attributes);
        try{
            unset($attributes['value']);
        }
        catch(OutOfBoundsException $e){
            
        }
    }

    public function getValue(){
        return $this->value;
    }

    protected function toStateInt(array $values){

        $i=0;
        $states = [];

        foreach($values as $option=>$selected){

            $states[] = (int)$selected;

            $i++;
        }

        return bindec(implode(array_reverse($states)));
    }

    protected function createStateArrayFromRequest($requestData){

        $sendedIndexes = [];

        if(is_array($requestData)){
            foreach($requestData as $key=>$stateId){
                $sendedIndexes[(int)$stateId] = true;
            }
        }

        $srcCount = count($this->getSrc());

        $stateArray = [];

        for($i=0; $i<$srcCount; $i++){
            if(isset($sendedIndexes[$i]) && $sendedIndexes[$i]){
                $stateArray[$i] = true;
            }
            else{
                $stateArray[$i] = false;
            }
        }

        return $stateArray;
    }

    function createStatedArrayFromInt($stateInt){

        $bits = decbin($stateInt);
        $bitArray = array_reverse(str_split($bits));
        $srcCount = count($this->getSrc());
        $stateArray = [];

        for($i=0; $i<$srcCount; $i++){

            if(isset($bitArray[$i]) && $bitArray[$i] == '1'){
                $stateArray[$i] = true;
            }
            else{
                $stateArray[$i] = false;
            }


        }

        return $stateArray;
    }

    public function setFromRequest($value){
        $this->stateArray = $this->createStateArrayFromRequest($value);
        $this->value = $this->toStateInt($this->stateArray);
    }

    public function setValue($value){

        $this->value = $value;
        $this->stateArray = $this->createStatedArrayFromInt((int)$value);

        return $this;
    }

    public function isItemSelected(SelectableProxy $item){

//         return !(bool)($item->position & $this->value);

        if(isset($this->stateArray[$item->position]) && $this->stateArray[$item->position] === true){
            return TRUE;
        }

        

        return FALSE;

    }

}
