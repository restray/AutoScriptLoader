<?php

namespace Restray\AutoScriptLoader;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AutoScriptLoaderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        // Add helper to create the scripts link
        View::composer('*', function ($view) {
            $base_path = explode('views/', $view->getPath());

            $list_name = explode('/', $base_path[1]);

            // Get the path
            $list_name = array_slice($list_name, 0, -1);

            $parent_path = $base_path[0].'views/'.implode('/', $list_name);

            if (file_exists($parent_path.'/script.js')) {
                $crypt = new Encrypter(env('APP_JS_KEY'));

                $encrypted_key = $crypt->encrypt($list_name);

                $view->with(
                    'path', '<script src="'.route('js').'?generate='.$encrypted_key.'"></script>'
                );
            } else {
                $view->with('path', null);
            }
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/autoscriptloader.php', 'autoscriptloader');

        // Register the service the package provides.
        $this->app->singleton('autoscriptloader', function ($app) {
            return new AutoScriptLoader;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['autoscriptloader'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/autoscriptloader.php' => config_path('autoscriptloader.php'),
        ], 'autoscriptloader.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/restray'),
        ], 'autoscriptloader.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/restray'),
        ], 'autoscriptloader.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/restray'),
        ], 'autoscriptloader.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
