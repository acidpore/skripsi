<?php

namespace App\Providers;

use App\Services\LearningStyleService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LearningStyleService::class, function () {
            return new LearningStyleService();
        });
    }

    public function boot(): void
    {
        //
    }
}
