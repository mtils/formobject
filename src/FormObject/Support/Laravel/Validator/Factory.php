<?php namespace FormObject\Support\Laravel\Validator;

use FormObject\Form;
use FormObject\Validator\FactoryInterface;

use Illuminate\Validation\Validator as LaravelValidator;

class Factory implements FactoryInterface
{

    public function createValidator(Form $form){

        // Look if a explicit validator creating method was created
        if(method_exists($form, 'createValidator')){

            $validator = $form->createValidator();

            if($validator instanceof Validator){
                return $validator;
            }

            if($validator instanceof LaravelValidator){
                $formValidator = new Validator($form);
                $formValidator->setSrcValidator($validator);
                return $formValidator;
            }

        }

        // Look for an validationRules() method
        elseif(method_exists($form, 'validationRules')){
            $rules = $form->validationRules();
            $validator = new Validator($form);
            $validator->setRules($rules);
            return $validator;
        }

        // Look for public validationRules property
        elseif(property_exists($form, 'validationRules')){
            $validator = new Validator($form);
            $validator->setRules($form->validationRules);
            return $validator;
        }

        return new Validator($form);
    }

}