<?php

declare(strict_types=1);

namespace Mailing\ValueObject\Mailjet;

final readonly class Body
{
    public function __construct(private array $messages = [], private bool $sandboxMode = false)
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
