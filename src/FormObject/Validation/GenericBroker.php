<?php namespace FormObject\Validation;

use FormObject\Form;

class GenericBroker implements BrokerInterface
{

    protected $form;

    protected $ruleNames = [];

    protected $errors = [];

    protected $validator;

    /**
     * The form will be passed to the broker
     *
     * @param \FormObject\Form $form
     * @return void
     **/
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     **/
    public function getRuleNames($fieldName)
    {
        if (isset($this->ruleNames[$fieldName])) {
            return $this->ruleNames[$fieldName];
        }
        return [];
    }

    public function setAllRuleNames($ruleNames)
    {
        $this->ruleNames = $ruleNames;
    }

    /**
     * Return true if a fieldname has errors or if no fieldName was passed there
     * are errors at all
     *
     * @param string $fieldName (optional)
     * @return bool
     **/
    public function hasErrors($fieldName=null)
    {
        return (bool)count($this->getErrors($fielName));
    }


    /**
     * Get all error messages for field named $fieldname. If no fieldname passed
     * get all error messages
     *
     * @param string $fieldName (optional)
     * @return array
     **/
    public function getErrors($fieldName=null)
    {
        if ($fieldName === null) {
            return $this->errors;
        }

        if (isset($this->errors[$fieldName])) {
            return $this->errors[$fieldName];
        }

        return [];
    }

    /**
     * Manually set Errors. This will be forwarded from the form to this broker.
     * So this can be anything form Symfony\MessageBag to ...
     *
     * @param mixed $errors
     * @return void
     **/
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Checks $data when form was submitted. If you like to throw exceptions
     * on validation errors this is the right place to do that
     *
     * @param array $data
     * @return void
     **/
    public function check(array $data){}

    /**
     * Return the setted validator, whatever validator it is
     *
     * @return mixed
     **/
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Set a validator of any type, the broker has to care about it
     *
     * @param mixed $validator
     * @return void
     **/
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

}