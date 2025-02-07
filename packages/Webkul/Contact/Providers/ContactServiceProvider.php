<?php

namespace Webkul\Contact\Providers;

use Illuminate\Support\ServiceProvider;
use Webkul\Contact\Repositories\PersonRepository;

class ContactServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PersonRepository::class);
    }
} 