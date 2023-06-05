<?php

declare(strict_types=1);

namespace Mailing\Validator;

use Mailing\Builder\EmailBuilder;

use function count;

class EmailValidator
{
    private bool $isValid = false;

    private array $cannotSendEmailReasons = [];

    public function __construct(private readonly EmailBuilder $emailBuilder)
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (count($this->emailBuilder->getTo()) === 0) {
            $this->cannotSendEmailReasons[] = 'No value for $to has been defined';
        }

        if (null === $this->emailBuilder->getSubject()) {
            $this->cannotSendEmailReasons[] = 'No subject defined';
        }

        if (null === $this->emailBuilder->getHtmlPart()) {
            $this->cannotSendEmailReasons[] = 'No HTML part defined';
        }

        if (null === $this->emailBuilder->getTextPart()) {
            $this->cannotSendEmailReasons[] = 'No Text part defined';
        }

        $this->isValid = count($this->cannotSendEmailReasons) === 0;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getCannotSendEmailReasons(): ?array
    {
        return $this->cannotSendEmailReasons;
    }
}
