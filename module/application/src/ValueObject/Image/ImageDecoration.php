<?php

declare(strict_types=1);

namespace Application\ValueObject\Image;

use JetBrains\PhpStorm\Pure;

use function sprintf;

final class ImageDecoration
{
    public const SHOW_IMAGE = 'image';
    public const SHOW_RAW   = 'raw';

    private static string $imageTemplate = '<img src="%s" class="img-fluid" alt="%s" %s %s>';

    public function __construct(private readonly string $show = self::SHOW_IMAGE, private readonly ?int $width = null, private readonly ?int $height = null)
    {
    }

    #[Pure] public static function fromArray(array $params): ImageDecoration
    {
        return new self(
            $params['show'] ?? self::SHOW_IMAGE,
            $params['width'] ?? null,
            $params['height'] ?? null
        );
    }

    public function parse(string $url): string
    {
        if ($this->show === self::SHOW_RAW) {
            return $url;
        }

        return sprintf(
            self::$imageTemplate,
            $url,
            'Alt',
            null !== $this->width ? 'width="' . $this->width . '"' : '',
            null !== $this->height ? 'height="' . $this->height . '"' : ''
        );
    }
}
