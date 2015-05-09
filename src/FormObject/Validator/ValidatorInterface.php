<?php

namespace FormObject\Validator;

interface ValidatorInterface{

    /**
     * Validates the data and returns true/false if data is valid/invalid.
     * The caller awaits that the data will be validated every time this method
     * is called. If the caller means it has to cache the validation result it
     * will do that on its own. If you like to throw validation or redirect
     * exceptions just do it. Form::getData() will fail and pass the exception
     *
     * @param array $data
     * @return bool
     **/
    public function validate(array $data);

    /**
     * @brief Returns if the value of this field has errors
     *        In many cases like in laravel hasErrors does not explicit mean
     *        that a validation has to occur to have errors/messages.
     *        The validator has to be aware of that.
     *
     *        If you do Redirect::to(x)->withErrors($errors) the next request
     *        will contain errors without ever validating the data
     *
     * @param string $fieldName
     * @return bool
     */
    public function hasErrors($fieldName);

    /**
     * @brief Returns a traversable of messages. Like hasErrors no real real
     *        validation has to occur
     * @see self::hasErrors
     * @param string $fieldName
     * @return \Traversable
     **/
    public function getMessages($fieldName);

    /**
     * @brief Returns a iterable of names describing the type of this field
     *        This is mainly for css classes which describe the type to add
     *        javascript validation
     * @param string $fieldName
     * @return \Traversable
     **/
    public function getRuleNames($fieldName);

    public function createValidationException();

}