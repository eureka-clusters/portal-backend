<?php

declare(strict_types=1);

namespace Application\ValueObject;

final readonly class SearchResult
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

    public function toArray(): array
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
