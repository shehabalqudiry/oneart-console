<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Components\Listener;
use INTCore\OneARTConsole\Str;

class ListenerGenerator extends Generator
{
    public function generate($name, $domain)
    {
        $listener = Str::listener($name);
        $domain = Str::domain($domain);
        $path = $this->findListenerPath($domain, $name);

        if ($this->exists($path)) {
            throw new Exception('Listener already exists');

            return false;
        }

        // Make sure the domain directory exists
        $this->createDomainDirectory($domain);

        // Create the listener
        $namespace = $this->findDomainListenersNamespace($domain, $name);
        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{listener}}', '{{namespace}}'],
            [$listener, $namespace],
            $content
        );

        $this->createFile($path, $content);

        return new Listener(
            $listener,
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
        $this->createDirectory($this->findDomainPath($domain) . '/Listeners');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        $stubName = '/stubs/listener.stub';
        return __DIR__ . $stubName;
    }

}
