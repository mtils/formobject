<?php namespace FormObject\Validator;

use ArrayAccess;
use FormObject\Form;

class ProxyValidator implements ValidatorInterface, ArrayAccess{

    protected $srcValidator;

    protected $fieldMap = array();

    protected $fieldMapReversed = array();

    protected $messagesCache = array();

    protected $validated = FALSE;

    protected $form;

    public function __construct(Form $form, ValidatorInterface $srcValidator=NULL){
        $this->form = $form;
        if($srcValidator){
            $this->setSrcValidator($srcValidator);
        }
    }

    public function getSrcValidator(){
        return $srcValidator;
    }

    public function setSrcValidator(ValidatorInterface $srcValidator){
        $this->srcValidator = $srcValidator;
        return $this;
    }

    public function map($srcName, $trgName){
        $this->fieldMap[$srcName] = $trgName;
        $this->fieldMapReversed[$trgName] = $srcName;
        return $this;
    }

    public function unmap($srcName){
        if(isset($this->fieldMap[$srcName])){
            $trgName = $this->fieldMap[$srcName];
            unset($this->fieldMap[$srcName]);
            unset($this->fieldMapReversed[$trgName]);
        }
        return $this;
    }

    public function validate($data){

        $mappedData = array();
        foreach($data as $fieldName=>$value){
            if(!$mappedName = $this->getSrcName($fieldName)){
                $mappedName = $fieldName;
            }
            $mappedData[$mappedName] = $value;
        }
        $res = $this->srcValidator->validate($mappedData);
//         echo "\n";
//         print_r($mappedData);
        echo "\nRESULT:"; var_dump($res);
        foreach($mappedData as $fieldName=>$value){
            $this->messagesCache[$fieldName] = $this->srcValidator->getMessages($fieldName);
        }
//         print_r($this->srcValidator->getValidator()->getRules());
//         print_r($this->messagesCache);
        $this->validated = TRUE;
        return $res;
    }

    public function getSrcName($trgName){
        if(isset($this->fieldMapReversed[$trgName])){
            return $this->fieldMapReversed[$trgName];
        }
    }

    public function getTrgName($srcName){
        return $this->offsetGet($srcName);
    }

    public function getValidator(){
        return $this->srcValidator->getValidator();
    }

    public function setValidator($validator){
        $this->srcValidator->setValidator($validator);
        return $this;
    }

    public function offsetGet($offset){
        return $this->fieldMap[$offset];
    }

    public function offsetSet($offset, $value){
        $this->map($offset, $value);
    }

    public function offsetExists($offset){
        return isset($this->fieldMap[$offset]);
    }

    public function offsetUnset($offset){
        $this->unmap($offset);
    }

    /**
     * @brief Returns if the value of this field is valid. Called by Field, so
     *        it will look by trgName
     * @param string $fieldName
     * @return bool
     */
    public function hasErrors($fieldName){

        if(!$mappedName = $this->getSrcName($fieldName)){
            $mappedName = $fieldName;
        }

        if(!$this->validated){
            $this->validate($this->form->data);
        }

        return $this->srcValidator->hasErrors($mappedName);

    }

    /**
     * @brief Returns a iterable of messages. Called by Field, so
     *        it will look by trgName
     * @param string $fieldName
     * @return \Iterable
     **/
    public function getMessages($fieldName){

        if(!$mappedName = $this->getSrcName($fieldName)){
            $mappedName = $fieldName;
        }
        if(!$this->validated){
            $this->validate($this->form->data);
        }
        return $this->srcValidator->getMessages($mappedName);

    }

    /**
     * @brief Returns a iterable of names describing the type of this field
     *        This is mainly for css classes which describe the type to add
     *        javascript validation
     * @param string $fieldName
     * @return \Iterable
     **/
    public function getRuleNames($fieldName){
        if(!$mappedName = $this->getSrcName($fieldName)){
            $mappedName = $fieldName;
        }
//         echo "<br/>getRuleNames $fieldName was translated to $mappedName<br/>";
        return $this->srcValidator->getRuleNames($mappedName);
    }

    public function createValidationException($validator){

        if(!$mappedName = $this->getSrcName($fieldName)){
            $mappedName = $fieldName;
        }

        return $this->srcValidator->createValidationException($fieldName);

    }
}