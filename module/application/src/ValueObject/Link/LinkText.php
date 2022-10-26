<?php

declare(strict_types=1);

namespace Application\ValueObject\Link;

use JetBrains\PhpStorm\Pure;

use function mb_strlen;
use function mb_substr;
use function trim;

final class LinkText
{
    public const DEFAULT_MAX_LENGTH = 50;

    private $text;
    private $title;
    private int $maxLength = self::DEFAULT_MAX_LENGTH;

    public function __construct(
        ?string $text = null,
        ?string $title = null,
        ?int $maxLength = null
    ) {
        $this->text  = $text ?? '';
        $this->title = $title ?? $this->text;
        if (null !== $maxLength) {
            $this->maxLength = $maxLength;
        }
    }

    #[Pure] public static function fromArray(array $params): LinkText
    {
        return new self(
            $params['text'] ?? null,
            $params['title'] ?? null,
            $params['maxLength'] ?? null
        );
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function parse(): string
    {
        return mb_strlen($this->text) > $this->maxLength
            ? trim(mb_substr($this->text, 0, $this->maxLength)) . '&hellip;'
            : $this->text;
    }
}
