<?php namespace FormObject\Support\Laravel;

use Illuminate\Support\ServiceProvider;
use \FormObject\Renderer\PhpRenderer;
use Config;

class FormObjectServiceProvider extends ServiceProvider {

    protected $templatePath = '';

    /**
        * Register the service provider.
        *
        * @return void
        */
    public function register()
    {
        $this->app->singleton('\FormObject\AdapterFactoryInterface', function($app)
        {
            $adapter = new AdapterFactoryLaravel();
            $renderer = new PhpRenderer();
            if($paths = Config::get('view.formpaths')){
                foreach($paths as $path){
                    $renderer->addPath($path);
                }
            }
            $adapter->setRenderer($renderer);
            $adapter->setEventDispatcher(new EventDispatcher($app['events']));
            return $adapter;
        });
    }

}
