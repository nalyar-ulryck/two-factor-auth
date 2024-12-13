<?php

namespace NalyarUlryck\TwoFactorAuth;

use Illuminate\Support\ServiceProvider;

class TwoFactorAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registra configurações do pacote
        $this->mergeConfigFrom(__DIR__ . '/./config/twofactor.php', 'twofactor');
    }

    public function boot()
    {
        // Carrega rotas
        // dd('aqui');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Carrega views do pacote
        $this->loadViewsFrom(__DIR__ . '/./resources/views', 'twofactor');

        // Publica configurações
        $this->publishes([
            __DIR__ . '/../config/twofactor.php' => config_path('twofactor.php'),
        ], 'config');

        $this->app['router']->aliasMiddleware('twofactor', \NalyarUlryck\TwoFactorAuth\Http\Middleware\TwoFactorAuthenticated::class);

        // Publica assets do pacote
        $this->publishes([
            __DIR__ . '/./resources/assets' => public_path('vendor/two-factor-auth'),
        ], 'assets');
    }
}
