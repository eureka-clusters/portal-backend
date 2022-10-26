<?php

declare(strict_types=1);

namespace Mailing\ValueObject\Mailjet;

final class Body
{
    public function __construct(private readonly array $messages = [], private readonly bool $sandboxMode = false)
    {
    }

    public function toArray(): array
    {
        return [
            'Messages'    => $this->messages,
            'SandboxMode' => $this->sandboxMode,
        ];
    }
}
