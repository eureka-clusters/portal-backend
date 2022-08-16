<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Organisation;
use Cluster\Provider\Organisation\TypeProvider;
use Laminas\Cache\Exception\ExceptionInterface;
use Laminas\Cache\Storage\Adapter\Redis;

class OrganisationProvider implements ProviderInterface
{
    public function __construct(
        private readonly Redis $cache,
        private readonly CountryProvider $countryProvider,
        private readonly TypeProvider $typeProvider
    ) {
    }

    /**
     * @param Organisation $organisation
     * @throws ExceptionInterface
     */
    public function generateArray($organisation): array
    {
        $cacheKey = $organisation->getResourceId();

        $organisationData = $this->cache->getItem($cacheKey);

        if (!$organisationData) {
            $organisationData = [
                'id' => $organisation->getId(),
                'slug' => $organisation->getSlug(),
                'name' => $organisation->getName(),
                'country' => $this->countryProvider->generateArray($organisation->getCountry()),
                'type' => $this->typeProvider->generateArray($organisation->getType()),
            ];

            $this->cache->setItem($cacheKey, $organisationData);
        }

        return $organisationData;
    }
}
