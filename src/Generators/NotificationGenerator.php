<?php

namespace INTCore\OneARTConsole\Generators;

use Exception;
use INTCore\OneARTConsole\Components\Notification;
use INTCore\OneARTConsole\Str;

class NotificationGenerator extends Generator
{
    public function generate($name, $domain)
    {
        $notification = Str::notification($name);
        $domain = Str::domain($domain);
        $path = $this->findNotificationPath($domain, $name);
        if ($this->exists($path)) {
            throw new Exception('Notification already exists');

            return false;
        }

        // Create the Notification
        $namespace = $this->findDomainNotificationNamespace($domain, $name);
        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{notification}}', '{{namespace}}'],
            [$notification, $namespace],
            $content
        );

        $this->createFile($path, $content);

        return new Notification(
            $notification,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            ($domain) ? $this->findDomain($domain) : null,
            $content
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub($isQueueable = false)
    {
        $stubName;
        if ($isQueueable) {
            $stubName = '/stubs/notification-queue.stub';
        } else {
            $stubName = '/stubs/notification.stub';

        }
        return __DIR__ . $stubName;
    }

}
