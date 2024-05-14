<?php

namespace INTCore\OneARTConsole\Components;

/**
 * Class Model
 *
 * @author Mark Rady <me@markrady.com>
 *
 * @package INTCore\OneARTConsole\Components
 */
class Model extends Component
{
    public function __construct($title, $namespace, $file, $path, $relativePath, Domain $domain, $content)
    {
        $this->setAttributes([
            'model' => $title,
            'namespace' => $namespace,
            'file' => $file,
            'path' => $path,
            'relativePath' => $relativePath,
            'content' => $content,
        ]);
    }
}
