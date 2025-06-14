<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Import Schema
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
         // Add the following line
    Schema::defaultStringLength(191);
    }
}
