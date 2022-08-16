<?php

declare(strict_types=1);

namespace Application\ValueObject;

use JetBrains\PhpStorm\ArrayShape;

final class SearchResult
{
    public function __construct(
        private readonly string $type,
        private readonly string $slug,
        private readonly string $name,
        private readonly ?string $title = null,
        private readonly ?string $description = null,
        private readonly ?string $organisationType = null,
        private readonly ?string $country = null,
        private readonly ?float $score = null,
    ) {
    }

    #[ArrayShape([
        'type' => "string",
        'slug' => "string",
        'name' => "string",
        'title' => "null|string",
        'description' => "null|string",
        'organisationType' => "null|string",
        'country' => "null|string",
        'score' => "null|float"
    ])] public function toArray(): array
    {
        return [
            'type' => $this->type,
            'slug' => $this->slug,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'organisationType' => $this->organisationType,
            'country' => $this->country,
            'score' => $this->score,
        ];
    }

    public function getScore(): ?float
    {
        return $this->score;
    }
}
