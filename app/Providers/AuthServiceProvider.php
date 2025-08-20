<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        } else {
            URL::forceScheme('http');
        }

        URL::forceRootUrl(config('app.url')); // Penting supaya verif link ke domain yang benar
    }
}
