<?php

namespace INTCore\OneARTConsole\Commands;

use Illuminate\Support\Str as StrHelper;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Filesystem;
use INTCore\OneARTConsole\Finder;
use INTCore\OneARTConsole\Generators\ListenerGenerator;
use INTCore\OneARTConsole\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class ListenerMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new listener in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Listener';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $generator = new ListenerGenerator();

        $domain = StrHelper::studly($this->argument('domain'));
        $title = $this->argument('listener');

        try {
            $listener = $generator->generate($title, $domain);

            $this->info(
                'Event class '.$title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$listener->relativePath.'</comment>'."\n"
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
            ['listener', InputArgument::REQUIRED, 'The listener\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain to be responsible for the listener.'],
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
        return __DIR__.'/../Generators/stubs/listener.stub';
    }

    /**
     * Parse the listener name.
     *  remove the listener.php suffix if found
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
