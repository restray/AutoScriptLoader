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
}
