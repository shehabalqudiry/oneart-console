<?php

namespace INTCore\OneARTConsole\Commands;

use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Finder;
use INTCore\OneARTConsole\Generators\EventGenerator;
use INTCore\OneARTConsole\Generators\JobGenerator;
use INTCore\OneARTConsole\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str as StrHelper;

class EventMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Event';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $generator = new EventGenerator();

        $domain = StrHelper::studly($this->argument('domain'));
        $title = $this->argument('event');

        try {
            $event = $generator->generate($title, $domain);

            $this->info(
                'Event class '.$title.' created successfully.'.
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
            ['event', InputArgument::REQUIRED, 'The event\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain to be responsible for the event.'],
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
        return __DIR__.'/../Generators/stubs/event.stub';
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
        return Str::event($name);
    }
}
