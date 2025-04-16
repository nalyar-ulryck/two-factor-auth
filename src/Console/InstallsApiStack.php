<?php

namespace NalyarUlryck\TwoFactorAuth\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

trait InstallsApiStack
{
    /**
     * Install the API Breeze stack.
     *
     * @return int|null
     */
    protected function installApiStack()
    {
        // (new Filesystem)->copyDirectory(__DIR__.'/../config', config_path());

        // Registra o provider para o comando atual
        app()->register(\NalyarUlryck\TwoFactorAuth\ApiServicProvider::class);

        // Adiciona o provider ao arquivo de configuração do usuário
        $this->addApiServiceProviderToAppConfig();

        return 0;
    }

    protected function addApiServiceProviderToAppConfig()
    {
        if (! file_exists(config_path('app.php'))) {
            return;
        }

        $providerClass = '\\NalyarUlryck\\TwoFactorAuth\\ApiServicProvider::class';

        // Verifica se o provider já está registrado
        $appConfig = file_get_contents(config_path('app.php'));
        if (Str::contains($appConfig, $providerClass)) {
            $this->components->info('Service Provider já está registrado');
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

        $this->components->info('Service Provider registrado em config/app.php');
    }
}
