<?php

namespace INTCore\OneARTConsole\Commands;

use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Finder;
use INTCore\OneARTConsole\Generators\ResourceGenerator;
use INTCore\OneARTConsole\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str as StrHelper;

class ResourceMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:resource {--C|collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $generator = new ResourceGenerator();

        $domain = StrHelper::studly($this->argument('domain'));
        $isCollection = $this->option('collection');
        $title = $this->argument('resource');

        try {
            $resource = $generator->generate($title, $domain, $isCollection);

            $this->info(
                'Resource class '.$title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$resource->relativePath.'</comment>'."\n"
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
            ['resource', InputArgument::REQUIRED, 'The resource\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain to be responsible for the job.'],
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
            ['collection', 'C', InputOption::VALUE_NONE, 'Whether a resource is collection or object.'],
        ];
    }


    /**
     * Parse the job name.
     *  remove the Job.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name, $isCollection = false)
    {
        return Str::resource($name, $isCollection);
    }
}
