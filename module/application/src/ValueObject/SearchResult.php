<?php

declare(strict_types=1);

namespace Application\ValueObject;

use JetBrains\PhpStorm\ArrayShape;

final class SearchResult
{
    public function __construct(
        private string $type,
        private string $slug,
        private string $name,
        private ?string $title = null,
        private ?string $description = null,
        private ?string $organisationType = null,
        private ?string $country = null,
        private ?float $score = null,
    ) {
    }

    #[ArrayShape([
        'type'             => "string",
        'slug'             => "string",
        'name'             => "string",
        'title'            => "null|string",
        'description'      => "null|string",
        'organisationType' => "null|string",
        'country'          => "null|string",
        'score'            => "null|float"
    ])] public function toArray(): array
    {
        return [
            'type'             => $this->type,
            'slug'             => $this->slug,
            'name'             => $this->name,
            'title'            => $this->title,
            'description'      => $this->description,
            'organisationType' => $this->organisationType,
            'country'          => $this->country,
            'score'            => $this->score,
        ];
    }

    public function getScore(): ?float
    {
        return $this->score;
    }
}
