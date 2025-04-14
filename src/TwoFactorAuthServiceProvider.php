<?php

namespace NalyarUlryck\TwoFactorAuth;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class TwoFactorAuthServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        // Registra configurações do pacote
        // $this->mergeConfigFrom(__DIR__ . '/./config/twofactor.php', 'twofactor');
    }

    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([Console\InstallCommand::class]);
        // Carrega rotas
        // $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Carrega views do pacote
        // $this->loadViewsFrom(__DIR__ . '/./resources/views', 'twofactor');

        // $this->app['router']->aliasMiddleware('twofactor', \NalyarUlryck\TwoFactorAuth\Http\Middleware\TwoFactorAuthenticated::class);

        // Publica assets do pacote
        // $this->publishes([
        //     __DIR__ . '/./resources/assets' => public_path('vendor/two-factor-auth'),
        // ], 'assets');

        // $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // $this->publishes([
        //     __DIR__ . '/./config/twofactor.php' => config_path('twofactor.php'),
        // ], 'config');
    }

    public function provides()
    {
        return [Console\InstallCommand::class];
    }
}
