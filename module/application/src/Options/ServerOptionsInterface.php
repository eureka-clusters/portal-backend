<?php

declare(strict_types=1);

namespace Application\Options;

/**
 * Interface ServerOptionsInterface
 */
interface ServerOptionsInterface
{
    public function getServerUrl(): string;

    public function setServerUrl(string $serverUrl): ModuleOptions;
}
