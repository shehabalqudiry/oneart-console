<?php

namespace INTCore\OneARTConsole\Commands;

use INTCore\OneARTConsole\Finder;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Generators\DomainGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class DomainMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The base namespace for this command.
     *
     * @var string
     */
    private $namespace;

    /**
     * The Services path.
     *
     * @var string
     */
    private $path;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:domain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new domain';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../Generators/stubs/service.stub';
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        try {
            $name = $this->argument('name');

            $generator = new DomainGenerator();
            $domain = $generator->generate($name);

            $this->info('Domain '.$domain->name.' created successfully.'."\n");

            $rootNamespace = $this->findRootNamespace();
            $domainNamespace = $this->findServiceNamespace($domain->name);

            $serviceProvider = $domainNamespace.'\\Providers\\'.$domain->name.'ServiceProvider';

            $this->info('Activate it by registering '.
                '<comment>'.$serviceProvider.'</comment> '.
                "\n".
                'in <comment>'.$rootNamespace.'\Foundation\Providers\ServiceProvider@register</comment> '.
                'with the following:'.
                "\n"
            );

            $this->info('<comment>$this->app->register(\''.$serviceProvider.'\');</comment>'."\n");
        } catch (\Exception $e) {
            $this->error($e->getMessage()."\n".$e->getFile().' at '.$e->getLine());
        }
    }

    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The domain name.'],
        ];
    }
}
