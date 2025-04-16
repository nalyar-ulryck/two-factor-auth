<?php

namespace NalyarUlryck\TwoFactorAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;

#[AsCommand(name: '2fa:install')]
class InstallCommand extends Command implements PromptsForMissingInput
{
    use InstallsApiStack, InstallsBladeStack;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '2fa:install {stack : The development stack that should be installed (blade and api)}
                            {--provider=google : Provedor de autenticação 2FA (google, authy, etc.)}
                        {--force : Forçar reconfiguração caso já esteja configurado}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Two Factor Auth controllers and resources';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $stack = $this->argument('stack');

        // Instalação baseada no stack escolhido
        $result = 0;
        if ($stack === 'api') {
            $result = $this->installApiStack();
        } elseif ($stack === 'blade') {
            $result = $this->installBladeStack();
        } else {
            $this->components->error('Invalid stack. Supported stacks are [blade] and [api].');
            return 1;
        }

        // Se a instalação foi bem-sucedida
        if ($result === 0) {
            $this->newLine();
            $this->components->info('✅ Two Factor Auth instalado com sucesso!');


            $this->newLine();
            $this->line('Próximos passos:');
            $this->components->bulletList([
                'Execute <fg=yellow>php artisan migrate</> para adicionar a coluna google2fa_secret',
                'Adicione a middleware <fg=yellow>2fa</> às rotas que deseja proteger',
                'Configure a rota padrão do seu sistema no arquivo <fg=yellow>config/twofactor.php</>'
            ]);
        }

        return $result;
    }

    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

    /**
     * Remove Tailwind dark classes from the given files.
     *
     * @return void
     */
    protected function removeDarkClasses(Finder $finder)
    {
        foreach ($finder as $file) {
            file_put_contents($file->getPathname(), preg_replace('/\sdark:[^\s"\']+/', '', $file->getContents()));
        }
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'stack' => fn() => select(
                label: 'Which 2fa stack would you like to install?',
                options: [
                    'blade' => 'Monolith',
                    'api' => 'API only',
                ],
                scroll: 2,
            ),
        ];
    }

}
