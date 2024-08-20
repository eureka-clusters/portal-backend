<?php

declare(strict_types=1);

namespace Mailing\ValueObject;

final readonly class Header
{
    public function __construct(private string $name, private string $value)
    {
    }

    public function toArray(): array
    {
        return [$this->name => $this->value];
    }
}
