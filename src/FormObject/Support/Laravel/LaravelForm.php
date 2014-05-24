<?php namespace FormObject\Support\Laravel;

use FormObject\Form;
use FormObject\Field\HiddenField;
use \App;

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
}
