<?php

namespace NalyarUlryck\TwoFactorAuth;

use Illuminate\Support\ServiceProvider;

class TwoFactorAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/./config/twofactor.php', 'twofactor');
    }

    public function boot()
    {

        $this->commands([Console\InstallCommand::class]);


        $this->app['router']->aliasMiddleware('twofactor', \NalyarUlryck\TwoFactorAuth\Http\Middleware\TwoFactorAuthenticated::class);

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__ . '/./config/twofactor.php' => config_path('twofactor.php'),
        ], 'config');

          // Carrega rotas
          $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

          // Carrega views do pacote
          $this->loadViewsFrom(__DIR__ . '/./resources/views', 'twofactor');

          // Publica assets do pacote
          $this->publishes([
              __DIR__ . '/./resources/assets' => public_path('vendor/two-factor-auth'),
          ], 'assets');
    }


    public function provides()
    {
        return [Console\InstallCommand::class];
    }
}
