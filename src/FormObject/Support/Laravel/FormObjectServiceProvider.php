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

        Form::setRenderer($renderer);

        Form::setEventDispatcher(new Dispatcher($this->app['events']));

    }

    public function boot(){

        $chain = new ActionUrlProviderChain();
        Form::setActionUrlProvider($chain);

        $currentUrlProvider = new CurrentActionUrlProvider(
            $this->app['url'],
            $this->app['request']
        );

        $chain->add($currentUrlProvider);
        $chain->add(new ResourceActionUrlProvider(
            $currentUrlProvider,
            $this->app['router'])
        );

        Form::setRequestProvider(new InputRequestProvider($this->app['request']));

        Form::setValidatorFactory(new Factory);

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
