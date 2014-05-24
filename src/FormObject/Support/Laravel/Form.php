<?php namespace FormObject\Support\Laravel;

use Illuminate\Support\Facade;

/**
 * @see \FormObject\Support\Laravel\LaravelForm
 */
class Form extends Facade {

        /**
         * Get the registered name of the component.
         *
         * @return string
         */
        protected static function getFacadeAccessor() { return 'form'; }

}
 
