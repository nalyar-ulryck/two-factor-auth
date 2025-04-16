<?php

namespace NalyarUlryck\TwoFactorAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
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

        $result = 0;
        if ($stack === 'api') {
            $result = $this->installApiStack();
        } elseif ($stack === 'blade') {
            $result = $this->installBladeStack();
        } else {
            $this->components->error('Invalid stack. Supported stacks are [blade] and [api].');
            return 1;
        }

        if ($result === 0) {
            $this->newLine();
            $this->components->info('✅ Two Factor Auth installed successfully!');

            $this->newLine();
            $this->line('Next steps:');
            $this->components->bulletList([
                'Run <fg=yellow>php artisan migrate</> to add the google2fa_secret column',
                'Add the middleware <fg=yellow>2fa</> to the routes you want to protect',
                'Set up your system’s default route in the file <fg=yellow>config/twofactor.php</>'
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
