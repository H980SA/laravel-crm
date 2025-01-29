<?php

namespace Webkul\Admin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class GanttServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'admin');

        Blade::componentNamespace('Webkul\\Admin\\View\\Components', 'admin');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }
} 