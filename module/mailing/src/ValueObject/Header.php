<?php

declare(strict_types=1);

namespace Mailing\ValueObject;

final class Header
{
    public function __construct(private readonly string $name, private readonly string $value)
    {
    }

    public function toArray(): array
    {
        return [$this->name => $this->value];
    }
}
