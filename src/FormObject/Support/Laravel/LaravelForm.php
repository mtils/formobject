<?php namespace FormObject\Support\Laravel;

use DomainException;
use FormObject\Form;
use FormObject\Field\HiddenField;
use \App;
use Illuminate\Validation\Validator;

class LaravelForm extends Form{

    protected $sessionToken = '';

    protected $sessionTokenName = '_token';

    public static function make($data=NULL, $useSessionToken=TRUE){
        $form = static::create($data);
        if($useSessionToken){
            $app = App::getFacadeRoot();
            $form->push(HiddenField::create('_token')
                            ->setValue($app['session.store']->getToken()));
        }
        return $form;
    }

    protected function createValidatorAdapter($validator){
        return new ValidatorAdapter($this, $validator);
    }

    public function setValidator($validator){

        if(!$validator instanceof Validator){
            throw new DomainException('LaravelForm Validators have to be Illuminate\Validation\Validator');
        }
        $validator->setAttributeNames($this->buildAttributeNames());

        parent::setValidator($validator);
    }

    protected function buildAttributeNames(){
        $attributeNames = array();
        foreach($this->getDataFields() as $field){
            $attributeNames[$field->getName()] = $field->getTitle();
        }
        return $attributeNames;
    }
}
