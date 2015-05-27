<?php namespace FormObject\Support\Laravel;

use Illuminate\Support\Facades\Facade;

class FormFacade extends Facade{

    protected static function getFacadeAccessor(){
        return 'formobject.factory';
    }
}