<?php namespace FormObject\RequestCaster;

class FlagsToIntCaster
{

    protected $flagCount;

    protected $possibleFlags = [];

    public function castToApplication($requestValue)
    {
        $stateArray = $this->createStateArrayFromRequest($requestValue);
        return $this->toStateInt($stateArray);
    }

    public function castToRequest($applicationValue)
    {
        return $this->createStatedArrayFromInt((int)$applicationValue);
    }

    public function isFlagSelected($flagName, $stateInt)
    {
        $key = array_search($flagName, $this->possibleFlags);
        $stateArray = $this->createStatedArrayFromInt((int)$stateInt);
        return (bool)$stateArray[$key];
    }

    protected function createStateArrayFromRequest($requestData){

        $sendedIndexes = [];

        if(is_array($requestData)){
            foreach($requestData as $key=>$stateId){
                $sendedIndexes[(int)$stateId] = true;
            }
        }

        $srcCount = $this->getFlagCount();

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

    protected function toStateInt(array $values){

        $i=0;
        $states = [];

        foreach($values as $option=>$selected){

            $states[] = (int)$selected;

            $i++;
        }

        return bindec(implode(array_reverse($states)));
    }

    function createStatedArrayFromInt($stateInt){

        $bits = decbin($stateInt);
        $bitArray = array_reverse(str_split($bits));
        $srcCount = $this->getFlagCount();
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

    public function getFlagCount()
    {
        if ($this->flagCount !== null)
        {
            return $this->flagCount;
        }

        return count($this->possibleFlags);
    }

    public function setFlagCount($count)
    {
        $this->flagCount = $count;
        return $this;
    }

    public function getPossibleFlags()
    {
        return $this->possibleFlags;
    }

    public function setPossibleFlags(array $flags)
    {
        $this->possibleFlags = $flags;
        return $this;
    }

}