<?php

namespace INTCore\OneARTConsole\Components;

/**
 * Class Request
 *
 * @author Mark Rady <me@markrady.com>
 *
 * @package INTCore\OneARTConsole\Components
 */
class Request extends Component
{
    public function __construct($title, $namespace, $file, $path, $relativePath, Domain $domain, $content)
    {
        $this->setAttributes([
            'request' => $title,
            'domain' => $domain,
            'namespace' => $namespace,
            'file' => $file,
            'path' => $path,
            'relativePath' => $relativePath,
            'content' => $content,
        ]);
    }
}
