<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Components\Event;
use INTCore\OneARTConsole\Str;

class EventGenerator extends Generator
{
    public function generate($name, $domain)
    {
        $event = Str::event($name);
        $domain = Str::domain($domain);
        $path = $this->findEventPath($domain, $name);

        if ($this->exists($path)) {
            throw new Exception('Event already exists');

            return false;
        }

        // Make sure the domain directory exists
        $this->createDomainDirectory($domain);

        // Create the job
        $namespace = $this->findDomainEventsNamespace($domain, $name);
        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{event}}', '{{namespace}}'],
            [$event, $namespace],
            $content
        );

        $this->createFile($path, $content);

        return new Event(
            $event,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            ($domain) ? $this->findDomain($domain) : null,
            $content
        );
    }

    /**
     * Create domain directory.
     *
     * @param string $domain
     */
    private function createDomainDirectory($domain)
    {
        $this->createDirectory($this->findDomainPath($domain) . '/Events');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        $stubName = '/stubs/event.stub';
        return __DIR__ . $stubName;
    }

}
