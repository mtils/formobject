<?php namespace FormObject\Support\LegacyFormObject\Validation;

use FormObject\Form;
use FormObject\Validator\FactoryInterface as ValidatorFactory;
use FormObject\Validation\BrokerInterface;
use Ems\Core\Patterns\HookableTrait;

class AutoValidatorBroker implements BrokerInterface
{

    use HookableTrait;

    public $throwExceptions = true;

    /**
     * @var \FormObject\Form
     **/
    protected $form;

    /**
     * @var \FormObject\Validator\ValidatorInterface
     **/
    protected $validator;

    /**
     * @var \FormObject\Validator\ValidatorFactoryInterface
     **/
    protected $validatorFactory;

    /**
     * @var array
     **/
    protected $errors = [];

    /**
     * @param \FormObject\Validator\FactoryInterface $validatorFactory
     **/
    public function __construct(ValidatorFactory $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * Returns the setted form
     *
     * @return \FormObject\Form
     **/
    public function getForm()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     *
     * @param \FormObject\Form
     * @return void
     **/
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    /**
     * Return all rule names for field $fieldName to use it as css classes. This
     * allows additional javascript validation. Rulenames have to be sparated
     * by a whitespace
     *
     * @return array
     **/
    public function getRuleNames($fieldName)
    {
        return $this->getValidator()->getRuleNames($fieldName);
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
        return $this->getValidator()->hasErrors($fieldName);
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
        return $this->getValidator()->getMessages($fieldName);
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
    public function check(array $data)
    {
        if (!$this->throwExceptions) {
            return;
        }

        $validator = $this->getValidator();

        $res = $validator->validate($data);

        if (!$res && $e = $validator->createValidationException()) {
            throw $e;
        }

    }

    /**
     * Return the setted validator, whatever validator it is
     *
     * @return mixed
     **/
    public function getValidator()
    {
        if (!$this->validator) {
            $this->setValidator(
                $this->validatorFactory->createValidator($this->form)
            );
        }

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
        $this->callBeforeListeners('setValidator', [$validator]);
        $this->validator = $validator;
        $this->callAfterListeners('setValidator', [$validator]);
    }

}
