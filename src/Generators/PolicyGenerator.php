<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Str;
use INTCore\OneARTConsole\Components\Policy;

class PolicyGenerator extends Generator
{
    /**
     * Generate the file.
     *
     * @param $name
     * @param $domain
     * @return Policy|bool
     * @throws Exception
     */
    public function generate($name, $domain)
    {
        $policy = Str::policy($name);
        $domain = Str::domain($domain);
        $path = $this->findPolicyPath($domain, $name);

        if ($this->exists($path)) {
            throw new Exception('Policy already exists');

            return false;
        }

        $this->createPolicyDirectory($domain);

        $namespace = $this->findPolicyNamespace($domain, $name);

        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{policy}}', '{{namespace}}', '{{foundation_namespace}}'],
            [$policy, $namespace, $this->findFoundationNamespace()],
            $content
        );

        $this->createFile($path, $content);

        return new Policy(
            $policy,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            $this->findDomain($domain),
            $content
        );
    }

    /**
     * Create Policies directory.
     */
    public function createPolicyDirectory($domain)
    {
        $this->createDirectory($this->findPoliciesPath($domain));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__ . '/../Generators/stubs/policy.stub';
    }
}
