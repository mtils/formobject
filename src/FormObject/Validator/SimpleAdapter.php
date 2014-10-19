<?php namespace FormObject\Validator;

class SimpleAdapter implements ValidatorAdapterInterface{

    protected $validator;

    public function getValidator(){
        return $this->validator;
    }

    public function setValidator($validator){
        $this->validator = $validator;
        return $this;
    }

    public function validate($data){
        return $this->validator->validate($data);
    }

    /**
     * @brief Returns if the value of this field is valid
     * @param string $fieldName
     * @return bool
     */
    public function hasErrors($fieldName){
        return $this->validator->hasErrors($fieldName);
    }

    /**
     * @brief Returns a iterable of messages
     * @param string $fieldName
     * @return \Iterable
     **/
    public function getMessages($fieldName){
        return $this->validator->getMessages($fieldName);
    }

    /**
     * @brief Returns a iterable of names describing the type of this field
     *        This is mainly for css classes which describe the type to add
     *        javascript validation
     * @param string $fieldName
     * @return \Iterable
     **/
    public function getRuleNames($fieldName){
        return $this->validator->getRuleNames($fieldName);
    }

    /**
     * @brief Creates the exception if you like exception based validation
     *
     * @param mixed $validator
     * @return Exception
     **/
    public function createValidationException($validator){
        return new ValidationException($validator->allMessages());
    }
}