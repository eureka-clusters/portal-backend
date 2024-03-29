<?php

declare(strict_types=1);

namespace Api\Paginator;

use Api\Provider\ProviderInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\Paginator\Adapter\AdapterInterface;

use function array_key_exists;

class DoctrineORMAdapter extends Paginator implements AdapterInterface
{
    public array $cache = [];

    private ProviderInterface $provider;

    public function setProvider(ProviderInterface $provider): DoctrineORMAdapter
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     */
    public function getItems($offset, $itemCountPerPage): array
    {
        if (
            array_key_exists(key: $offset, array: $this->cache)
            && array_key_exists(key: $itemCountPerPage, array: $this->cache[$offset])
        ) {
            return $this->cache[$offset][$itemCountPerPage];
        }

        $this->getQuery()->setFirstResult(firstResult: $offset);
        $this->getQuery()->setMaxResults(maxResults: $itemCountPerPage);

        if (!array_key_exists(key: $offset, array: $this->cache)) {
            $this->cache[$offset] = [];
        }

        $results = [];
        foreach ($this->getQuery()->getResult() as $result) {
            $results[] = $this->provider->generateArray($result);
        }

        $this->cache[$offset][$itemCountPerPage] = $results;

        return $this->cache[$offset][$itemCountPerPage];
    }
}
