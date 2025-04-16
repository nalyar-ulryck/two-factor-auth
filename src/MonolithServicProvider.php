<?php

namespace NalyarUlryck\TwoFactorAuth;

use Illuminate\Support\ServiceProvider;

class MonolithServicProvider extends ServiceProvider
{
    public function register()
    {
        // Registra configurações do pacote
        $this->mergeConfigFrom(__DIR__ . '/./config/twofactor.php', 'twofactor');

    }

    public function boot()
    {
        // Carrega rotas
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Carrega views do pacote
        $this->loadViewsFrom(__DIR__ . '/./resources/views', 'twofactor');

        // Publica assets do pacote
        $this->publishes([
            __DIR__ . '/./resources/assets' => public_path('vendor/two-factor-auth'),
        ], 'assets');

    }

}
