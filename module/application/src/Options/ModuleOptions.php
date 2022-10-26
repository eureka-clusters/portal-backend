<?php

declare(strict_types=1);

namespace Application\Options;

use Laminas\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements ServerOptionsInterface
{
    protected string $serverUrl = 'https://api.eurekaclusters.eu';

    public function getServerUrl(): string
    {
        return $this->serverUrl;
    }

    public function setServerUrl(string $serverUrl): ModuleOptions
    {
        $this->serverUrl = $serverUrl;
        return $this;
    }
}
