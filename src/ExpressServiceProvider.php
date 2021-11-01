<?php

namespace Pharaoh\Express;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ExpressServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/express.php', 'express');

        $this->loadViewsFrom(__DIR__ . '/Views', 'pharaoh_express');

        $this->publishes(
            [
                __DIR__ . '/../config/express.php' => config_path('express.php')
            ],
            'express-config'
        );
    }

    public function register()
    {
        parent::register();

        $loader = AliasLoader::getInstance();
        $loader->alias('express', 'Pharaoh\Express\Express');
    }
}
