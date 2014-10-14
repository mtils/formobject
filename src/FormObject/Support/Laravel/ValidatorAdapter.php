<?php namespace FormObject\Support\Laravel;

use FormObject\Validator\ValidatorAdapterInterface;
use FormObject\Form;
use DomainException;
use Illuminate\Validation\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Session;

class ValidatorAdapter implements ValidatorAdapterInterface{

    protected $validator;
    protected $form;
    protected $validated=FALSE;
    protected $messageBag;

    public function __construct(Form $form, $validator){
        $this->validator = $validator;
        $this->form = $form;
    }

    public function validate($data){

        $this->validator->setData($data);
        // This starts Laravels validating
        $res = $this->validator->passes();
        $this->validated = TRUE;
        return $res;
    }

    public function getValidator(){
        return $this->validator;
    }

    public function setValidator($validator){

        if(!$validator instanceof Validator){
            throw new DomainException('LaravelForm Validators have to be Illuminate\Validation\Validator not '.get_class($validator));
        }

        $validator->setAttributeNames($this->buildAttributeNames($this->form));
        $this->validator = $validator;

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
            if($messages = $this->validator->messages()){
                return $messages;
            }
        }

        return new MessageBag;

    }

    public function createValidationException($validator){
        if($messages = $validator->messages()){
            return new ValidationException($messages);
        }
    }

    public function getRuleNames($fieldName){

        $ruleNames = array();

        foreach($this->validator->getRules() as $key=>$rules){
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

} 
