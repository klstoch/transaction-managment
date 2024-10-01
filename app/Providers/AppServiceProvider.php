<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
     * @throws RequiredParameterMissedException
     */
    public function boot(): void
    {
        $config = config('money');
        LibConfig::getInstance($config);
    }
}
