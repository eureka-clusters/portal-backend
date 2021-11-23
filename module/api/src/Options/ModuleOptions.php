<?php

declare(strict_types=1);

namespace Api\Options;

use Laminas\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    protected string $cryptoKey = 'this-is-a-default-key';

    public function getCryptoKey(): string
    {
        return $this->cryptoKey;
    }

    public function setCryptoKey(string $cryptoKey): void
    {
        $this->cryptoKey = $cryptoKey;
    }
}
