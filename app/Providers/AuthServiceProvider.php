<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Logbook;
use App\Policies\LogbookPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Logbook::class => LogbookPolicy::class,
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
