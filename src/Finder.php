<?php

namespace INTCore\OneARTConsole;

use Exception;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use INTCore\OneARTConsole\Components\Feature;
use INTCore\OneARTConsole\Components\Service;
use INTCore\OneARTConsole\Components\Domain;
use INTCore\OneARTConsole\Components\Job;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Illuminate\Support\Str as StrHelper;

define('DS', DIRECTORY_SEPARATOR);

trait Finder
{
    /**
     * The name of the source directory.
     *
     * @var string
     */
    protected $srcDirectoryName = 'src';

    public function fuzzyFind($query)
    {
        $finder = new SymfonyFinder();

        $files = $finder->in($this->findServicesRootPath() . '/*/Features') // features
        ->in($this->findDomainsRootPath() . '/*/Jobs') // jobs
        ->name('*.php')
            ->files();

        $matches = [
            'jobs'     => [],
            'features' => [],
        ];

        foreach ($files as $file) {
            $base = $file->getBaseName();
            $name = str_replace(['.php', ' '], '', $base);

            $query = str_replace(' ', '', trim($query));

            similar_text($query, mb_strtolower($name), $percent);

            if ($percent > 35) {
                if (strpos($base, 'Feature.php')) {
                    $matches['features'][] = [$this->findFeature($name)->toArray(), $percent];
                } elseif (strpos($base, 'Job.php')) {
                    $matches['jobs'][] = [$this->findJob($name)->toArray(), $percent];
                }
            }
        }

        // sort the results by their similarity percentage
        $this->sortFuzzyResults($matches['jobs']);
        $this->sortFuzzyResults($matches['features']);

        $matches['features'] = $this->mapFuzzyResults($matches['features']);
        $matches['jobs'] = array_map(function ($result) {
            return $result[0];
        }, $matches['jobs']);

        return $matches;
    }

    /**
     * Sort the fuzzy-find results.
     *
     * @param array &$results
     *
     * @return bool
     */
    private function sortFuzzyResults(&$results)
    {
        return usort($results, function ($resultLeft, $resultRight) {
            return $resultLeft[1] < $resultRight[1];
        });
    }

    /**
     * Map the fuzzy-find results into the data
     * that should be returned.
     *
     * @param array $results
     *
     * @return array
     */
    private function mapFuzzyResults($results)
    {
        return array_map(function ($result) {
            return $result[0];
        }, $results);
    }

    /**
     * Get the source directory name.
     * In a microservice installation this will be `app`. `src` otherwise.
     *
     * @return string
     */
    public function getSourceDirectoryName()
    {
        if (file_exists(base_path() . '/' . $this->srcDirectoryName)) {
            return $this->srcDirectoryName;
        }

        return 'app';
    }

    /**
     * Determines whether this is a lucid microservice installation.
     *
     * @return bool
     */
    public function isMicroservice()
    {
        return !($this->getSourceDirectoryName() === $this->srcDirectoryName);
    }

    /**
     * Get the namespace used for the application.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function findRootNamespace()
    {
        // read composer.json file contents to determine the namespace
        $composer = json_decode(file_get_contents(base_path() . '/composer.json'), true);

        // see which one refers to the "src/" directory
        foreach ($composer['autoload']['psr-4'] as $namespace => $directory) {
            if ($directory === $this->getSourceDirectoryName() . '/') {
                return trim($namespace, '\\');
            }
        }

        throw new Exception('App namespace not set in composer.json');
    }

    /**
     * Find the namespace of the foundation.
     *
     * @return string
     */
    public function findFoundationNamespace()
    {
        return 'INTCore\OneARTFoundation';
    }

    /**
     * get the root of the source directory.
     *
     * @return string
     */
    public function findSourceRoot()
    {
        return ($this->isMicroservice()) ? app_path() : base_path() . '/' . $this->srcDirectoryName;
    }

    /**
     * Find the root path of all the services.
     *
     * @return string
     */
    public function findServicesRootPath()
    {
        return $this->findSourceRoot() . '/Domains';
    }

    /**
     * Find the namespace for the given service name.
     *
     * @param string $service
     *
     * @return string
     */
    public function findServiceNamespace($service)
    {
        $root = $this->findRootNamespace();
        return (!$service) ? $root : "$root\\Domains\\$service";
    }

    /**
     * Find the path to the directory of the given domain name.
     * In the case of a microservice domain installation this will be app path.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findDomainPath($domain)
    {
        return (!$domain) ? app_path() : $this->findServicesRootPath() . "/$domain";
    }

    /**
     * Find the features root path in the given service.
     *
     * @param string $service
     *
     * @return string
     */
    public function findFeaturesRootPath($service)
    {
        return $this->findDomainPath($service) . '/Features';
    }

    /**
     * Find the file path for the given feature.
     *
     * @param string $domain
     * @param string $feature
     *
     * @return string
     */
    public function findFeaturePath(string $domain, string $feature)
    {
        return $this->findFeaturesRootPath($domain) . "/{$feature}Feature.php";
    }

    /**
     * Find the test file path for the given feature.
     *
     * @param string $domain
     * @param string $feature
     *
     * @return string
     */
    public function findFeatureTestPath($domain, $test)
    {
        $root = ($domain) ? $this->findDomainPath($domain) . '/Tests' : base_path() . '/tests';

        return "$root/Features/$test.php";
    }

    /**
     * Find the namespace for features in the given service.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findFeatureNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\\Features' . $completed_namespace;
    }

    /**
     * Find the namespace for features tests in the given service.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findFeatureTestNamespace(string $domain)
    {
        return $this->findDomainNamespace($domain) . '\\Tests\\Features';
    }

    /**
     * Find the root path of domains.
     *
     * @return string
     */
    public function findDomainsRootPath()
    {
        return $this->findSourceRoot() . '/Domains';
    }

    /**
     * Get the list of domains.
     *
     * @return \Illuminate\Support\Collection;
     */
    public function listDomains()
    {
        $finder = new SymfonyFinder();
        $directories = $finder
            ->depth(0)
            ->in($this->findDomainsRootPath())
            ->directories();

        $domains = new Collection();
        foreach ($directories as $directory) {
            $name = $directory->getRelativePathName();

            $domain = new Domain(
                Str::realName($name),
                $this->findDomainNamespace($name),
                $directory->getRealPath(),
                $this->relativeFromReal($directory->getRealPath())
            );

            $domains->push($domain);
        }

        return $domains;
    }

    /**
     * Find the path for the given job name.
     *
     * @param string $domain
     * @param string $job
     *
     * @return string
     */
    public function findJobPath(string $domain, string $job)
    {
        return $this->findJobRootPath($domain) . "/{$job}Job.php";
    }

    /**
     * Find the job root path in the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findJobRootPath($domain)
    {
        return $this->findDomainPath($domain) . '/Jobs';
    }

    /**
     * Find the namespace for the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findDomainNamespace($domain)
    {
        return $this->findRootNamespace() . '\\Domains\\' . $domain;
    }

    /**
     * Find the namespace for the given domain's Jobs.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findDomainJobsNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\Jobs' . $completed_namespace;
    }

    /**
     * Find the namespace for the given domain's Jobs.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findDomainJobsTestsNamespace($domain)
    {
        return $this->findDomainNamespace($domain) . '\Tests\Jobs';
    }

    /**
     * Get the path to the tests of the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findDomainTestsPath($domain)
    {
        if ($this->isMicroservice()) {
            return base_path() . DS . 'tests' . DS . 'Domains' . DS . $domain;
        }

        return $this->findDomainPath($domain) . DS . 'Tests';
    }

    /**
     * Find the test path for the given job.
     *
     * @param string $domain
     * @param string $job
     *
     * @return string
     */
    public function findJobTestPath($domain, $jobTest)
    {
        $root = ($domain) ? $this->findDomainPath($domain) . '/Tests' : base_path() . '/tests';

        return "$root/Jobs/$jobTest.php";

    }

    /**
     * Find the path for the give controller class.
     *
     * @param string $domain
     * @param string $controller
     *
     * @return string
     */
    public function findControllerPath($domain, $controller)
    {
        return $this->findDomainPath($domain) . "/Http/Controllers/{$controller}Controller.php";
    }

    /**
     * Find the namespace of controllers in the given service.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findControllerNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);
        return $this->findDomainNamespace($domain) . '\\Http\\Controllers' . $completed_namespace;
    }

    /**
     * Generate the completion of namespace for each type of classes
     *
     * @param $path
     * @return string
     */
    public static function generateFullQualifiedNamespace($path)
    {
        $delimiters = explode('/', $path);
        $class_name = array_pop($delimiters);

        if (count($delimiters) == 0) return '';

        if (count($delimiters) == 1) return '\\' . $delimiters[0];

        return '\\' . implode('\\', $delimiters);
    }

    /**
     * Get the list of services.
     *
     * @return \Illuminate\Support\Collection
     */
    public function listServices()
    {
        $services = new Collection();

        if (file_exists($this->findServicesRootPath())) {
            $finder = new SymfonyFinder();

            foreach ($finder->directories()->depth('== 0')->in($this->findServicesRootPath())->directories() as $dir) {
                $realPath = $dir->getRealPath();
                $services->push(new Service($dir->getRelativePathName(), $realPath, $this->relativeFromReal($realPath)));
            }
        }

        return $services;
    }

    /**
     * Find the domain for the given domain name.
     *
     * @param string $domain
     *
     * @return \INTCore\OneARTConsole\Components\Domain
     */
    public function findDomain($domain)
    {
        $finder = new SymfonyFinder();
        $dirs = $finder->name($domain)->in($this->findServicesRootPath())->directories();
        if ($dirs->count() < 1) {
            throw new Exception('Service "' . $domain . '" could not be found.');
        }

        foreach ($dirs as $dir) {
            $path = $dir->getRealPath();

            return new Domain(Str::domain($domain), $path, $this->relativeFromReal($path));
        }
    }

    /**
     * Find the domain for the given domain name.
     *
     * @param string $domain
     *
     * @return \INTCore\OneARTConsole\Components\Domain
     */
    // public function findDomain($domain)
    // {
    //     $finder = new SymfonyFinder();
    //     $dirs = $finder->name($domain)->in($this->findDomainsRootPath())->directories();
    //     if ($dirs->count() < 1) {
    //         throw new Exception('Domain "'.$domain.'" could not be found.');
    //     }

    //     foreach ($dirs as $dir) {
    //         $path = $dir->getRealPath();

    //         return  new Domain(
    //             Str::service($domain),
    //             $this->findDomainNamespace($domain),
    //             $path,
    //             $this->relativeFromReal($path)
    //         );
    //     }
    // }

    /**
     * Find the feature for the given feature name.
     *
     * @param string $name
     *
     * @return \INTCore\OneARTConsole\Components\Feature
     */
    public function findFeature(string $name)
    {
        $name = Str::feature($name);
        $fileName = "$name.php";

        $finder = new SymfonyFinder();
        $files = $finder->name($fileName)->in($this->findServicesRootPath())->files();
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $serviceName = strstr($file->getRelativePath(), DS, true);
            $service = $this->findService($serviceName);
            $content = file_get_contents($path);

            return new Feature(
                Str::realName($name, '/Feature/'),
                $fileName,
                $path,
                $this->relativeFromReal($path),
                $service,
                $content
            );
        }
    }

    /**
     * Find the feature for the given feature name.
     *
     * @param string $name
     *
     * @return \INTCore\OneARTConsole\Components\Feature
     */
    public function findJob($name)
    {
        $name = Str::job($name);
        $fileName = "$name.php";

        $finder = new SymfonyFinder();
        $files = $finder->name($fileName)->in($this->findDomainsRootPath())->files();
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $domainName = strstr($file->getRelativePath(), DIRECTORY_SEPARATOR, true);
            $domain = $this->findDomain($domainName);
            $content = file_get_contents($path);

            return new Job(
                Str::realName($name, '/Job/'),
                $this->findDomainJobsNamespace($domainName),
                $fileName,
                $path,
                $this->relativeFromReal($path),
                $domain,
                $content
            );
        }
    }

    /**
     * Get the list of features,
     * optionally withing a specified service.
     *
     * @param string $serviceName
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \Exception
     */
    public function listFeatures($serviceName = '')
    {
        $services = $this->listServices();

        if (!empty($serviceName)) {
            $services = $services->filter(function ($service) use ($serviceName) {
                return $service->name === $serviceName || $service->slug === $serviceName;
            });

            if ($services->isEmpty()) {
                throw new InvalidArgumentException('Service "' . $serviceName . '" could not be found.');
            }
        }

        $features = [];
        foreach ($services as $service) {
            $serviceFeatures = new Collection();
            $finder = new SymfonyFinder();
            $files = $finder
                ->name('*Feature.php')
                ->in($this->findFeaturesRootPath($service->name))
                ->files();
            foreach ($files as $file) {
                $fileName = $file->getRelativePathName();
                $title = Str::realName($fileName, '/Feature.php/');
                $realPath = $file->getRealPath();
                $relativePath = $this->relativeFromReal($realPath);

                $serviceFeatures->push(new Feature($title, $fileName, $realPath, $relativePath, $service));
            }

            // add to the features array as [service_name => Collection(Feature)]
            $features[$service->name] = $serviceFeatures;
        }

        return $features;
    }

    /**
     * Find the model root path in the given service.
     *
     * @param string $service
     *
     * @return string
     */
    public function findModelRootPath($service)
    {
        return $this->findDomainPath($service) . '/Models';
    }

    /**
     * Get the path to the passed model.
     *
     * @param string $domain
     * @param string $model
     *
     * @return string
     */
    public function findModelPath(string $domain, string $model)
    {
        return $this->findModelRootPath($domain) . "/$model.php";
    }

    /**
     * Get the path to the policies directory.
     *
     * @return string
     */
    public function findPoliciesPath($domain)
    {
        return $this->findDomainPath($domain) . '/Policies';
    }

    /**
     * Get the path to the passed policy.
     *
     * @param string $policy
     * @param string $domain
     *
     * @return string
     */
    public function findPolicyPath(string $domain, string $policy)
    {
        return $this->findPoliciesPath($domain) . "/{$policy}Policy.php";
    }

    /**
     * Get the path to the request directory of a specific service.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findRequestsPath(string $domain)
    {
        return $this->findDomainPath($domain) . '/Http/Requests';
    }

    /**
     * Get the path to a specific request.
     *
     * @param string $domain
     * @param string $request
     *
     * @return string
     */
    public function findRequestPath(string $domain, string $request)
    {
        return $this->findRequestsPath($domain) . "/{$request}Request.php";
    }

    /**
     * Get the namespace for the Models.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findModelNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\\Models' . $completed_namespace;
    }

    /**
     * Get the namespace for Policies.
     *
     * @param string $domain
     * @param string $class_path
     * @return mixed
     */
    public function findPolicyNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\\Policies' . $completed_namespace;
    }

    /**
     * Get the requests namespace for the service passed in.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findRequestsNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\\Http\\Requests' . $completed_namespace;
    }

    /**
     * Get the relative version of the given real path.
     *
     * @param string $path
     * @param string $needle
     *
     * @return string
     */
    protected function relativeFromReal($path, $needle = '')
    {
        if (!$needle) {
            $needle = $this->getSourceDirectoryName() . '/';
        }

        return strstr($path, $needle);
    }

    /**
     * Get the path to the Composer.json file.
     *
     * @return string
     */
    protected function getComposerPath()
    {
        return app()->basePath() . '/composer.json';
    }

    /**
     * Get the path to the given configuration file.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getConfigPath($name)
    {
        return app()['path.config'] . '/' . $name . '.php';
    }

    /***********************************************************************************************
     *********************************** Mail *************************************************
     ***********************************************************************************************/


    /**
     * Find the path for the given mail name.
     *
     * @param string $domain
     * @param string $mail
     *
     * @return string
     */
    public function findMailPath(string $domain, string $mail)
    {
        return $this->findMailRootPath($domain) . "/{$mail}Mail.php";

    }

    /**
     * Find the mail root path in the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findMailRootPath(string $domain)
    {
        return $this->findDomainPath($domain) . '/Mails';
    }

    /**
     * Find the namespace for the given domain's Mails.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findDomainMailNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\Mails' . $completed_namespace;
    }

    /***********************************************************************************************
     *********************************** Notifications *************************************************
     ***********************************************************************************************/


    /**
     * Find the path for the given notification name.
     *
     * @param string $domain
     * @param string $notification
     *
     * @return string
     */
    public function findNotificationPath(string $domain, string $notification)
    {
        return $this->findNotificationRootPath($domain) . "/{$notification}Notification.php";

    }

    /**
     * Find the notification root path in the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findNotificationRootPath(string $domain)
    {
        return $this->findDomainPath($domain) . '/Notifications';
    }

    /**
     * Find the namespace for the given domain's Notifications.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findDomainNotificationNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\Notifications' . $completed_namespace;
    }

    /***********************************************************************************************
     *********************************** Resources *************************************************
     ***********************************************************************************************/


    /**
     * Find the path for the given resource name.
     *
     * @param string $domain
     * @param string $resource
     *
     * @return string
     */
    public function findResourcePath(string $domain, string $resource)
    {
        return $this->findResourceRootPath($domain) . "/{$resource}.php";

    }

    /**
     * Find the resource root path in the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findResourceRootPath(string $domain)
    {
        return $this->findDomainPath($domain) . '/Http/Resources';
    }

    /**
     * Find the namespace for the given domain's resource.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findDomainResourceNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\Http\Resources' . $completed_namespace;
    }

    /***********************************************************************************************
     *********************************** Events *************************************************
     ***********************************************************************************************/


    /**
     * Find the path for the given event name.
     *
     * @param string $domain
     * @param string $event
     *
     * @return string
     */
    public function findEventPath(string $domain, string $event)
    {
        return $this->findEventRootPath($domain) . "/{$event}Event.php";

    }

    /**
     * Find the namespace for the given domain's events.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findDomainEventsNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\Events' . $completed_namespace;
    }

    /**
     * Find the event root path in the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findEventRootPath(string $domain)
    {
        return $this->findDomainPath($domain) . '/Events';
    }



    /***********************************************************************************************
     *********************************** Listeners *************************************************
     ***********************************************************************************************/

    /**
     * Find the path for the given listener name.
     *
     * @param string $domain
     * @param string $listener
     *
     * @return string
     */
    public function findListenerPath(string $domain, string $listener)
    {
        return $this->findListenerRootPath($domain) . "/{$listener}Listener.php";

    }

    /**
     * Find the namespace for the given domain's listener.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findDomainListenersNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\Listeners' . $completed_namespace;
    }

    /**
     * Find the listener root path in the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findListenerRootPath(string $domain)
    {
        return $this->findDomainPath($domain) . '/Listeners';
    }



    /***********************************************************************************************
     *********************************** Repository *************************************************
     ***********************************************************************************************/

    /**
     * Find the path for the given repository name.
     *
     * @param string $domain
     * @param string $repository
     *
     * @return string
     */
    public function findRepositoryPath(string $domain, string $repository)
    {
        return $this->findRepositoryRootPath($domain) . "/{$repository}Repository.php";

    }

    /**
     * Find the namespace for the given domain's Jobs.
     *
     * @param string $domain
     * @param string $class_path
     * @return string
     */
    public function findDomainRepositoryNamespace(string $domain, string $class_path)
    {
        $completed_namespace = static::generateFullQualifiedNamespace($class_path);

        return $this->findDomainNamespace($domain) . '\Repositories' . $completed_namespace;
    }

    /**
     * Find the event root path in the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findRepositoryRootPath(string $domain)
    {
        return $this->findDomainPath($domain) . '/Repositories';
    }

}
