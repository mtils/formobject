<?php namespace FormObject\Support\Laravel;

use Illuminate\Support\ServiceProvider;
use Config;

class FormObjectServiceProvider extends ServiceProvider {

//         /**
//          * Indicates if loading of the provider is deferred.
//          *
//          * @var bool
//          */
//         protected $defer = false;

        protected $templatePath = '';

        /**
         * Register the service provider.
         *
         * @return void
         */
        public function register()
        {
                $this->app->singleton('FormObject\AdapterFactoryInterface', function($app)
                {
                        $adapter = new AdapterFactoryLaravel();
                        $renderer = new \FormObject\Renderer\PhpRenderer();
                        if($paths = Config::get('view.formpaths')){
                            foreach($paths as $path){
                                $renderer->addPath($path);
                            }
                        }
                        $adapter->setRenderer($renderer);
                        return $adapter;
                });
        }

//         /**
//          * Get the services provided by the provider.
//          *
//          * @return array
//          */
//         public function provides()
//         {
//                 return array('FormObject\AdapterFactoryInterface');
//         }

}
 
