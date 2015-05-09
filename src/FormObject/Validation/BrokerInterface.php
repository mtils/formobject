<?php namespace FormObject\Validation;

use FormObject\Form;

interface BrokerInterface
{


    /**
     * The form will be passed to the broker
     *
     * @param \FormObject\Form $form
     * @return void
     **/
    public function setForm(Form $form);

    /**
     * Return all rule names for field $fieldName to use it as css classes. This
     * allows additional javascript validation.
     *
     * @return array
     **/
    public function getRuleNames($fieldName);

    /**
     * Return true if a fieldname has errors or if no fieldName was passed there
     * are errors at all
     *
     * @param string $fieldName (optional)
     * @return bool
     **/
    public function hasErrors($fieldName=null);


    /**
     * Get all error messages for field named $fieldname. If no fieldname passed
     * get all error messages
     *
     * @param string $fieldName (optional)
     * @return array
     **/
    public function getErrors($fieldName=null);

    /**
     * Manually set Errors. This will be forwarded from the form to this broker.
     * So this can be anything form Symfony\MessageBag to ...
     *
     * @param mixed $errors
     * @return void
     **/
    public function setErrors($errors);

    /**
     * Checks $data when form was submitted. If you like to throw exceptions
     * on validation errors this is the right place to do that
     *
     * @param array $data
     * @return void
     **/
    public function check(array $data);

    /**
     * Return the setted validator, whatever validator it is
     *
     * @return mixed
     **/
    public function getValidator();

    /**
     * Set a validator of any type, the broker has to care about it
     *
     * @param mixed $validator
     * @return void
     **/
    public function setValidator($validator);

}