<?php

namespace INTCore\OneARTConsole\Commands;

use Illuminate\Support\Facades\Artisan;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Finder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class MigrationMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Migration class in a domain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $migration = $this->argument('migration');

        $path = $this->relativeFromReal($this->findDomainPath($domain) . "/database/migrations");

        $output = shell_exec('php artisan make:migration '.$migration.' --path='.$path);

        $this->info($output);
        $this->info("\n".'Find it at <comment>'.$path.'</comment>'."\n");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['migration', InputArgument::REQUIRED, 'The migration\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain in which the migration should be generated.'],
        ];
    }
}
