<?php namespace FormObject\Support\LegacyFormObject\Validation;

use FormObject\Validator\FactoryInterface as ValidatorFactory;
use FormObject\Validation\BrokerFactoryInterface;

class LegacyBrokerFactory implements BrokerFactoryInterface
{

    protected $validatorFactory;

    public function __construct(ValidatorFactory $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * Create a validation broker object for $form
     *
     * @param \FormObject\Form $form
     * @return \FormObject\Validation\BrokerInterface
     **/
    public function createBroker(Form $form)
    {
        $broker = new AutoValidatorBroker($this->validatorFactory);
        $broker->setForm($form);
        return $broker;
    }

}