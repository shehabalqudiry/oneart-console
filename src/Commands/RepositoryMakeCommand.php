<?php

namespace INTCore\OneARTConsole\Commands;

use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Finder;
use INTCore\OneARTConsole\Generators\EventGenerator;
use INTCore\OneARTConsole\Generators\JobGenerator;
use INTCore\OneARTConsole\Generators\RepositoryGenerator;
use INTCore\OneARTConsole\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str as StrHelper;

class RepositoryMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $generator = new RepositoryGenerator();

        $domain = StrHelper::studly($this->argument('domain'));
        $title = $this->argument('repo');

        try {
            $event = $generator->generate($title, $domain);

            $this->info(
                'Repository class '.$title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$event->relativePath.'</comment>'."\n"
            );
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return [
            ['repo', InputArgument::REQUIRED, 'The repository\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain to be responsible for the repository.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/../Generators/stubs/repository.stub';
    }

    /**
     * Parse the event name.
     *  remove the Event.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return Str::repository($name);
    }
}
