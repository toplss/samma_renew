<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SsoService;

class SsoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SsoService::class, function ($app) {
            return new SsoService();
        });
    }
}
