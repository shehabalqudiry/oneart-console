<?php

namespace INTCore\OneARTConsole\Components;

/**
 * Class Policy
 *
 * @author Mark Rady <me@markrady.com>
 *
 * @package INTCore\OneARTConsole\Components
 */
class Policy extends Component
{
    public function __construct($title, $namespace, $file, $path, $relativePath, Domain $service, $content)
    {
        $this->setAttributes([
            'policy' => $title,
            'namespace' => $namespace,
            'file' => $file,
            'path' => $path,
            'service' => $service,
            'relativePath' => $relativePath,
            'content' => $content,
        ]);
    }
}
