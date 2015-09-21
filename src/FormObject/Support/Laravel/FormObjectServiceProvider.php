<?php namespace FormObject\Support\Laravel;


use Signal\Support\Laravel\IlluminateBus;

use Illuminate\Support\ServiceProvider;
use Collection\NestedArray;

use FormObject\Renderer\RendererInterface;
use FormObject\Http\ActionUrlProviderChain;
use FormObject\Form;
use FormObject\Field\HiddenField;
use FormObject\Support\Laravel\Validator\Factory as ValidatorFactory;
use FormObject\Support\LegacyFormObject\Validation\AutoValidatorBroker;

use FormObject\Support\Laravel\Http\InputRequestProvider;
use FormObject\Support\Laravel\Http\CurrentActionUrlProvider;
use FormObject\Support\Laravel\Http\ResourceActionUrlProvider;
use FormObject\Factory;

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

        Form::provideValidationBroker(function(Form $form){

            $broker = new AutoValidatorBroker(new ValidatorFactory());
            $broker->setForm($form);
            return $broker;

        });

        Form::provideRequestProvider(function(){
            return $this->getRequestProvider();
        });


        Form::provideUrlProvider(function(){
            return $this->getActionUrlProvider();
        });

        Form::provideRenderer(function(){
            return $this->getRenderer();
        });

        Form::provideAdditionalNamer(function($chain){
            $chain->append($this->getTranslationNamer());
        });

        $this->app['events']->listen('form.renderer-changed', function($renderer) {
            $this->addFormPaths($renderer);
        });

        $this->app->alias('formobject.factory', 'FormObject\Factory');

        $this->app->singleton('formobject.factory', function($app){
            $factory = Form::getFactory();
            $factory->createFieldsWith(function($class) {
                return $this->app->make($class);
            });
            return $factory;
        });

        $this->app->afterResolving('XType\Casting\Contracts\InputCaster', function($caster) {
            $this->registerInputCasters($caster);
        });


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

    public function getTranslationNamer()
    {
        return $this->app->make('FormObject\Support\Laravel\Naming\TranslationNamer');
    }

    protected function registerInputCasters($caster)
    {

        $caster->add('no_leading_underscore', function($input) {

            $cleaned = [];
            foreach ($input as $key=>$value) {
                // tokens, _method...
                if (!starts_with($key,'_')) {
                    $cleaned[$key] = $value;
                }
            }

            return $cleaned;

        });

        $caster->add('no_actions', function($input) {

            $cleaned = [];
            foreach ($input as $key=>$value) {
                // form actions
                if (!str_contains($key,'-')) {
                    $cleaned[$key] = $value;
                }
            }

            return $cleaned;

        });

        $caster->add('no_confirmations', function($input) {

            $cleaned = [];
            foreach ($input as $key=>$value) {
                // form actions
                if (!ends_with($key, '_confirmation')) {
                    $cleaned[$key] = $value;
                }
            }

            return $cleaned;

        });

        $caster->add('nested', function($input) {
            return NestedArray::toNested($input, '.');
        });

        $caster->add('dotted', function($input) {

            $cleaned = [];
            foreach ($input as $key=>$value) {
                // form naming to dots
                $cleaned[str_replace('__','.', $key)] = $value;
            }

            return $cleaned;

        });

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
