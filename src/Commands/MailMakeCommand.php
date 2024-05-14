<?php

namespace INTCore\OneARTConsole\Commands;

use Illuminate\Support\Facades\Artisan;
use INTCore\OneARTConsole\Command;
use INTCore\OneARTConsole\Finder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use INTCore\OneARTConsole\Generators\MailGenerator;

class MailMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new mail class in a domain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $generator = new MailGenerator();

        $domain = $this->argument('domain');
        $mail = $this->argument('mail');

        try {
            $mailer = $generator->generate($mail, $domain);

            $this->info(
                'Mail class '.$mailer->className.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$mailer->relativePath.'</comment>'."\n"
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
            ['mail', InputArgument::REQUIRED, 'The mail\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain in which the migration should be generated.'],
        ];
    }
}
