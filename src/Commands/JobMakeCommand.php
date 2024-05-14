<?php

namespace INTCore\OneARTConsole\Commands;

use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Finder;
use INTCore\OneARTConsole\Generators\JobGenerator;
use INTCore\OneARTConsole\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str as StrHelper;

class JobMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:job {--Q|queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Job in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $generator = new JobGenerator();

        $domain = StrHelper::studly($this->argument('domain'));
//        $title = $this->parseName($this->argument('job'));

        $title = $this->argument('job');
        $isQueueable = $this->option('queue');

        try {
            $job = $generator->generate($title, $domain, $isQueueable);

            $this->info(
                'Job class '.$title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$job->relativePath.'</comment>'."\n"
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
            ['job', InputArgument::REQUIRED, 'The job\'s name.'],
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
            ['queue', 'Q', InputOption::VALUE_NONE, 'Whether a job is queueable or not.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/../Generators/stubs/job.stub';
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
    protected function parseName($name)
    {
        return Str::job($name);
    }
}
