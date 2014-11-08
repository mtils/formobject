<?php namespace FormObject\Support\Laravel;

use Illuminate\Support\ServiceProvider;

use FormObject\Renderer\PhpRenderer;
use FormObject\Http\ActionUrlProviderChain;
use FormObject\Form;
use FormObject\Field\HiddenField;

use FormObject\Support\Laravel\Http\InputRequestProvider;
use FormObject\Support\Laravel\Http\CurrentActionUrlProvider;
use FormObject\Support\Laravel\Http\ResourceActionUrlProvider;

use FormObject\Support\Laravel\Validator\Factory;
use FormObject\Support\Laravel\Event\Dispatcher;
use Config;

class FormObjectServiceProvider extends ServiceProvider {

    /**
        * Register the service provider.
        *
        * @return void
        */
    public function register()
    {

        $renderer = new PhpRenderer();

        if($paths = $this->app['config']->get('view.formpaths')){
            foreach($paths as $path){
                $renderer->addPath($path);
            }
        }

        $chain = new ActionUrlProviderChain();
        Form::setActionUrlProvider($chain);

        $chain->add(new CurrentActionUrlProvider);
        $chain->add(new ResourceActionUrlProvider);

        Form::setRequestProvider(new InputRequestProvider);
        Form::setRenderer($renderer);
        Form::setValidatorFactory(new Factory);

        Form::setEventDispatcher(new Dispatcher($this->app['events']));

        Form::addFormModifier( function(Form $form){

            // Trigger auto action setter
            $form->getAction();

            $verb = strtoupper($form->getVerb());

            if(in_array($verb, ['PUT','PATCH','DELETE'])){
                $form->push(HiddenField::create('_method')
                                         ->setValue($verb));
            }

        });

    }

}
