<?php

namespace NalyarUlryck\TwoFactorAuth;

use Illuminate\Support\ServiceProvider;

class ApiServicProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        // Carrega rotas
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

    }

}
