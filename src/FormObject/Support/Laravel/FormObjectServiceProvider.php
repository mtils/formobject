<?php namespace FormObject\Support\Laravel;


use Signal\Support\Laravel\IlluminateBus;

use Illuminate\Support\ServiceProvider;

use FormObject\Renderer\RendererInterface;
use FormObject\Http\ActionUrlProviderChain;
use FormObject\Form;
use FormObject\Field\HiddenField;

use FormObject\Support\Laravel\Http\InputRequestProvider;
use FormObject\Support\Laravel\Http\CurrentActionUrlProvider;
use FormObject\Support\Laravel\Http\ResourceActionUrlProvider;

use FormObject\Support\Laravel\Validator\Factory;

class FormObjectServiceProvider extends ServiceProvider
{

    /**
        * Register the service provider.
        *
        * @return void
        */
    public function register()
    {

        Form::setStaticEventBus(new IlluminateBus($this->app['events']));

        $this->app['events']->listen('form.requestprovider-requested', function() {
            return $this->getRequestProvider();
        });

        $this->app['events']->listen('form.validatorFactory-requested', function() {
            return $this->getValidatorFactory();
        });

        $this->app['events']->listen('form.urlprovider-requested', function() {
            return $this->getActionUrlProvider();
        });

        $this->app['events']->listen('form.renderer-requested', function() {
            return $this->getRenderer();
        });

        $this->app['events']->listen('form.renderer-changed', function($renderer) {
            $this->addFormPaths($renderer);
        });

    }

    public function getValidatorFactory()
    {
        return new Factory;
    }

    public function getActionUrlProvider()
    {

        $chain = new ActionUrlProviderChain();

        $currentUrlProvider = new CurrentActionUrlProvider(
            $this->app['url'],
            $this->app['request']
        );
        $chain->add($currentUrlProvider);

        $chain->add(new ResourceActionUrlProvider(
            $currentUrlProvider,
            $this->app['router'])
        );

        return $chain;

    }

    public function getRequestProvider()
    {
        return new InputRequestProvider($this->app['request']);
    }

    public function getRenderer()
    {
        return $this->app->make('FormObject\Renderer\PhpRenderer');
    }

    public function addFormPaths(RendererInterface $renderer)
    {
        if(!$paths = $this->app['config']->get('view.formpaths')){
            return;
        }

        foreach($paths as $path){
            $renderer->addPath($path);
        }

    }

    public function boot(){

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
