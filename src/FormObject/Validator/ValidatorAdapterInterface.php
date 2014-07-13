<?php namespace FormObject\Validator;

interface ValidatorAdapterInterface{

    public function getValidator();

    public function setValidator($validator);

    public function validate($data);

    /**
     * @brief Returns if the value of this field is valid
     * @param string $fieldName
     * @return bool
     */
    public function hasErrors($fieldName);

    /**
     * @brief Returns a iterable of messages
     * @param string $fieldName
     * @return \Iterable
     **/
    public function getMessages($fieldName);

    /**
     * @brief Returns a iterable of names describing the type of this field
     *        This is mainly for css classes which describe the type to add
     *        javascript validation
     * @param string $fieldName
     * @return \Iterable
     **/
    public function getRuleNames($fieldName);

}