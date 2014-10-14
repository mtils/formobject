<?php namespace FormObject\Support\Laravel;

use Illuminate\Support\ServiceProvider;
use FormObject\Renderer\PhpRenderer;
use FormObject\Form;
use Config;

class FormObjectServiceProvider extends ServiceProvider {

    /**
        * Register the service provider.
        *
        * @return void
        */
    public function register()
    {

        $adapter = new AdapterFactoryLaravel();
        $renderer = new PhpRenderer();

        if($paths = $this->app['config']->get('view.formpaths')){
            foreach($paths as $path){
                $renderer->addPath($path);
            }
        }

        $adapter->setRenderer($renderer);
        $adapter->setEventDispatcher(new EventDispatcher($this->app['events']));

        Form::setGlobalAdapter($adapter);

        $this->app->instance('FormObject\AdapterFactoryInterface', $adapter);
    }

}
