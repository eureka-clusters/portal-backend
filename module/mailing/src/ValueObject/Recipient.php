<?php

declare(strict_types=1);

namespace Mailing\ValueObject;

use JetBrains\PhpStorm\ArrayShape;
use Laminas\Mail\Address;
use Laminas\Validator\EmailAddress;

use function count;
use function sprintf;

final class Recipient
{
    public function __construct(private readonly string $name, private readonly string $email)
    {
    }

    public function isValid(): bool
    {
        return count($this->isInvalidReasons()) === 0;
    }

    public function isInvalidReasons(): array
    {
        $invalidReasons = [];

        $emailValidator = new EmailAddress();

        if (!$emailValidator->isValid($this->email)) {
            $invalidReasons[] = sprintf('Email address (%s) is invalid', $this->email);
        }

        return $invalidReasons;
    }

    #[ArrayShape(['Email' => "string", 'Name' => "string"])] public function toArray(): array
    {
        return [
            'Email' => $this->email,
            'Name' => $this->name,
        ];
    }

    public function toAddress(): Address
    {
        return new Address($this->email, $this->name);
    }
}
