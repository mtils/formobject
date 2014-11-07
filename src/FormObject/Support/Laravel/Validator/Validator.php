<?php namespace FormObject\Support\Laravel\Validator;

use FormObject\Validator\ValidatorInterface;
use FormObject\Form;
use DomainException;
use Illuminate\Validation\Validator as LaravelValidator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use \Validator as LaravelFactory;
use Session;

class Validator implements ValidatorInterface{

    protected $srcValidator;

    protected $form;

    protected $validated=FALSE;

    protected $messageBag;

    protected $rules = [];

    protected $throwExceptions = TRUE;

    public function __construct(Form $form){
        $this->form = $form;
    }

    public function validate(array $data){

        $this->getSrcValidator()->setData($data);

        // This starts Laravels validating
        $res = $this->getSrcValidator()->passes();
        $this->validated = TRUE;

        if($this->throwExceptions && !$res){
            $exception = $this->createValidationException($this->getSrcValidator());
            throw $exception;
        }

        return $res;
    }

    public function getSrcValidator(){

        if(!$this->srcValidator){
            $srcValidator = LaravelFactory::make([], $this->getRules());
            $this->setSrcValidator($srcValidator);
        }
        return $this->srcValidator;
    }

    public function setSrcValidator(LaravelValidator $srcValidator){

        $srcValidator->setAttributeNames($this->buildAttributeNames($this->form));
        $this->srcValidator = $srcValidator;

        return $this;
    }

    public function hasErrors($fieldName){
        return (bool)count($this->getMessageBag()->get($fieldName));
    }

    public function getMessages($fieldName){
        return $this->getMessageBag()->get($fieldName);
    }

    protected function getMessageBag(){

        if($errors = Session::get('errors')){
            if($errors instanceof ViewErrorBag && $errors->hasBag('default')){
                return $errors->getBag('default');
            }
        }

        if($this->form->needsValidation()){
            if(!$this->validated){
                $this->validate($this->form->data);
            }
            if($messages = $this->getSrcValidator()->messages()){
                return $messages;
            }
        }

        return new MessageBag;

    }

    public function createValidationException($srcValidator){
        if($messages = $srcValidator->messages()){
            return new ValidationException($messages);
        }
    }

    public function getRuleNames($fieldName){

        $ruleNames = array();

        foreach($this->getSrcValidator()->getRules() as $key=>$rules){
            if($key == $fieldName){
                foreach($rules as $rule){
                    $ruleNames[] = $this->ruleToName($rule);
                }
                break;
            }
        }
        return $ruleNames;
    }

    protected function ruleToName($rule){
        return preg_replace('/[^a-zA-Z0-9]+/u', '-', $rule);
    }

    protected function buildAttributeNames($form){
        $attributeNames = array();
        foreach($form->getDataFields() as $field){
            $attributeNames[$field->getName()] = $field->getTitle();
        }
        return $attributeNames;
    }

    public function getRules(){
        return $this->rules;
    }

    public function setRules(array $rules){
        $this->rules = $rules;
        return $this;
    }

    public function getRule($field){
        if(isset($this->rules[$field])){
            return $this->rules[$field];
        }
    }

    public function setRule($field, $rule){
        $this->rules[$field] = $rule;
        return $this;
    }

    public function removeRule($field){
        unset($this->rules[$field]);
    }

    public function addRules(array $rules){

        foreach($rules as $field=>$rule){
            $this->setRule($field, $rule);
        }
        return $this;

    }

    public function throwsExceptions(){
        return $this->throwExceptions;
    }

    public function forceExceptions($force=TRUE){
        $this->throwExceptions = $force;
    }

} 
