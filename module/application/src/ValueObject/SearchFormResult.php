<?php

declare(strict_types=1);

namespace Application\ValueObject;

use Doctrine\Common\Collections\Criteria;
use JetBrains\PhpStorm\Pure;
use Laminas\Json;

use function array_key_exists;
use function base64_decode;
use function base64_encode;
use function in_array;
use function strtoupper;

final class SearchFormResult
{
    public function __construct(
        private string $order = '',
        private string $direction = Criteria::ASC,
        private ?string $query = null,
        private array $filter = [],
    ) {
    }

    #[Pure] public static function fromArray(array $params): SearchFormResult
    {
        return new self(
            order: $params['order'] ?? 'default',
            direction: $params['direction'] ?? Criteria::ASC,
            query: $params['query'] ?? null,
            filter: $params['filter'] ?? []
        );
    }

    public function updateFromEncodedFilter(string $encodedFilter): SearchFormResult
    {
        $filter = (array)Json\Json::decode(
            encodedValue: base64_decode(string: $encodedFilter),
            objectDecodeType: Json\Json::TYPE_ARRAY
        );

        $this->filter = (array)($filter['filter'] ?? []);
        $this->query = $filter['query'] ?? null;

        return $this;
    }

    public function setQuery(?string $query): SearchFormResult
    {
        $this->query = $query;
        return $this;
    }

    public function setFilter(array $filter): SearchFormResult
    {
        $this->filter = $filter;
        return $this;
    }

    public function getHash(): string
    {
        return base64_encode(string: Json\Json::encode(valueToEncode: $this->toArray()));
    }

    public function toArray(): array
    {
        return [
            'order' => $this->order,
            'direction' => $this->direction,
            'query' => $this->query,
            'filter' => $this->filter
        ];
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function getFilterByKey(string $key, $default = []): mixed
    {
        return $this->filter[$key] ?? $default;
    }

    public function setFilterByKey(string $key, mixed $value, bool $force = false): SearchFormResult
    {
        //Only set the value when we force it or when it does not exist
        if ($force || !array_key_exists(key: $key, array: $this->filter)) {
            $this->filter[$key] = $value;
        }

        return $this;
    }

    public function hasFilterByKey(string $key): bool
    {
        return array_key_exists(key: $key, array: $this->filter) && '' !== $this->filter[$key] && !empty($this->filter[$key]);
    }

    public function hasQuery(): bool
    {
        return !empty($this->query);
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function setDirection(string $direction): SearchFormResult
    {
        $this->direction = strtoupper(string: $direction);
        return $this;
    }

    public function setOrder(string $order): SearchFormResult
    {
        $this->order = $order;
        return $this;
    }

    public function getDirection(): string
    {
        $direction = strtoupper(string: $this->direction);

        if (!in_array(needle: $direction, haystack: [Criteria::ASC, Criteria::DESC], strict: true)) {
            return Criteria::DESC;
        }

        return $direction;
    }

    public function addFilters(array $filters): SearchFormResult
    {
        foreach ($filters as $key => $value) {
            $this->addFilter(key: $key, value: $value);
        }

        return $this;
    }

    public function addFilter(string $key, mixed $value): SearchFormResult
    {
        if (array_key_exists(key: $key, array: $this->filter)) {
            $this->filter[$key] += $value;
        } else {
            $this->filter[$key] = $value;
        }

        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }
}