<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Components\Mail;
use INTCore\OneARTConsole\Str;

class MailGenerator extends Generator
{
    public function generate($name, $domain)
    {
        $mail = Str::email($name);
        $domain = Str::domain($domain);
        $path = $this->findMailPath($domain, $name);
        if ($this->exists($path)) {
            throw new Exception('Mail already exists');

            return false;
        }

        // Create the mail
        $namespace = $this->findDomainMailNamespace($domain, $name);
        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{mail}}', '{{namespace}}'],
            [$mail, $namespace],
            $content
        );

        $this->createFile($path, $content);


        return new Mail(
            $mail,
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
        $this->createDirectory($this->findDomainPath($domain).'/Jobs');
        $this->createDirectory($this->findDomainTestsPath($domain).'/Jobs');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub($isQueueable = false)
    {
        $stubName = '/stubs/mail.stub';
        return __DIR__.$stubName;
    }

}
