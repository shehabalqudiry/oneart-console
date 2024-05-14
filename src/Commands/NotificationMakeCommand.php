<?php

namespace INTCore\OneARTConsole\Commands;

use Illuminate\Support\Facades\Artisan;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Finder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use INTCore\OneARTConsole\Generators\NotificationGenerator;

class NotificationMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new notification class in a domain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $generator = new NotificationGenerator();

        $domain = $this->argument('domain');
        $title = $this->argument('notification');
        
        try {
            $notification = $generator->generate($title, $domain);

            $this->info(
                'Job class '.$notification->className.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$notification->relativePath.'</comment>'."\n"
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
    protected function getArguments()
    {
        return [
            ['notification', InputArgument::REQUIRED, 'The notification\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain in which the migration should be generated.'],
        ];
    }
}
