<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Str;
use INTCore\OneARTConsole\Components\Feature;


class FeatureGenerator extends Generator
{
    public function generate($name, $domain, array $jobs = [])
    {
        $feature_name = Str::feature($name);
        $domain = Str::domain($domain);
        if (empty($domain))
            throw new Exception('domain not specified!');

        $path = $this->findFeaturePath($domain, $name);

        if ($this->exists($path)) {
            throw new Exception('Feature already exists!');

            return false;
        }
        $namespace = $this->findFeatureNamespace($domain, $name);
        $content = file_get_contents($this->getStub());

        $useJobs = ''; // stores the `use` statements of the jobs
        $runJobs = ''; // stores the `$this->run` statements of the jobs

        foreach ($jobs as $index => $job) {
            $useJobs .= 'use '.$job['namespace'].'\\'.$job['className'].";\n";
            $runJobs .= "\t\t".'$this->run('.$job['className'].'::class);';

            // only add carriage returns when it's not the last job
            if ($index != count($jobs) - 1) {
                $runJobs .= "\n\n";
            }
        }

        $content = str_replace(
            ['{{feature}}', '{{namespace}}', '{{foundation_namespace}}', '{{use_jobs}}', '{{run_jobs}}'],
            [$feature_name, $namespace, $this->findFoundationNamespace(), $useJobs, $runJobs],
            $content
        );

        $this->createFile($path, $content);

        // generate test file
         $this->generateTestFile($feature_name, $domain, $namespace);

        return new Feature(
            $feature_name,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            ($domain) ? $this->findDomain($domain) : null,
            $content
        );
    }

    /**
     * Generate the test file.
     *
     * @param  string $feature
     * @param  string $domain
     */
    private function generateTestFile($feature, $domain, $feature_namespace)
    {
        $content = file_get_contents($this->getTestStub());

        $namespace = $this->findFeatureTestNamespace($domain);
        $featureNamespace = $feature_namespace."\\$feature";
        $testClass = $feature.'Test';

        $content = str_replace(
            ['{{namespace}}', '{{testclass}}', '{{feature}}', '{{feature_namespace}}'],
            [$namespace, $testClass, mb_strtolower($feature), $featureNamespace],
            $content
        );

        $path = $this->findFeatureTestPath($domain, $testClass);

        $this->createFile($path, $content);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/feature.stub';
    }

    /**
     * Get the test stub file for the generator.
     *
     * @return string
     */
    private function getTestStub()
    {
        return __DIR__.'/stubs/feature-test.stub';
    }
}
