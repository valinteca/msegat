<?php

namespace Valinteca\Msegat;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MsegatServiceProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * Facades
         */
        $this->app->bind('msegat', function($app) {
            return new Msegat();
        });
      
        $loader = AliasLoader::getInstance();
        $loader->alias('Msegat', "Valinteca\\Msegat\\Facades\\Msegat");

        /**
         * Config
         */
        $this->mergeConfigFrom(
            __DIR__ . '/../config/msegat.php', 'msegat'
        );
    }

    public function boot()
    {
        /**
         * Config
         */
        $this->publishes([
            __DIR__ . '/../config/msegat.php' => config_path('msegat.php'),
        ]);
    
        /**
         * Translations
         */
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'msegat');
 
        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/msegat'),
        ]);
    }
}