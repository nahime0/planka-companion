<?php

namespace App\Providers;

use App\Services\PlankaUrlService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PlankaUrlService::class, function ($app) {
            return new PlankaUrlService(
                config('planka')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
