<?php

namespace NalyarUlryck\TwoFactorAuth\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

trait InstallsBladeStack
{
    /**
     * Install the Blade Breeze stack.
     *
     * @return int|null
     */
    protected function installBladeStack()
    {

        (new Filesystem)->copyDirectory(__DIR__.'/../config', config_path());

        // Registra o provider para o comando atual
        app()->register(\NalyarUlryck\TwoFactorAuth\MonolithServicProvider::class);

        // Adiciona o provider ao arquivo de configuração do usuário
        $this->addBladeServiceProviderToAppConfig();

        // Publica os assets, etc. se necessário
        $this->call('vendor:publish', [
            '--provider' => 'NalyarUlryck\TwoFactorAuth\MonolithServicProvider',
            '--tag' => ['config', 'assets'],
        ]);

        return 0;
    }

    /**
     * Adiciona o Service Provider ao arquivo config/app.php
     */
    protected function addBladeServiceProviderToAppConfig()
    {
        if (! file_exists(config_path('app.php'))) {
            return;
        }

        $providerClass = '\\NalyarUlryck\\TwoFactorAuth\\MonolithServicProvider::class';

        // Verifica se o provider já está registrado
        $appConfig = file_get_contents(config_path('app.php'));
        if (Str::contains($appConfig, $providerClass)) {
            $this->components->info('Service Provider is already registered');
            return;
        }

        // Adiciona o provider ao array de providers
        file_put_contents(
            config_path('app.php'),
            str_replace(
                "        App\\Providers\RouteServiceProvider::class,\n",
                "        App\\Providers\RouteServiceProvider::class,\n        $providerClass,\n",
                $appConfig
            )
        );

        $this->components->info('Service Provider registered in config/app.php');
    }
}
