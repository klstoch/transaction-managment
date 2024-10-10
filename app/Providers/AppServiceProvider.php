<?php

namespace App\Providers;

use App\Exceptions\ErrorHandler;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\LibConfig;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExceptionHandler::class, ErrorHandler::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @throws RequiredParameterMissedException
     */
    public function boot(): void
    {
        $config = config('money');
        LibConfig::getInstance($config);
    }
}
