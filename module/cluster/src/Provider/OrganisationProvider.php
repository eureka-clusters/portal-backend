<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Organisation;
use Cluster\Provider\Organisation\TypeProvider;
use Laminas\Cache\Storage\Adapter\Redis;

class OrganisationProvider implements ProviderInterface
{
    public function __construct(
        private Redis $cache,
        private CountryProvider $countryProvider,
        private TypeProvider $typeProvider
    ) {
    }

    /**
     * @param Organisation $organisation
     * @return array
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function generateArray($organisation): array
    {
        $cacheKey = $organisation->getResourceId();

        $organisationData = $this->cache->getItem($cacheKey);

        if (!$organisationData) {
            $organisationData = [
                'id'      => $organisation->getId(),
                'slug'    => $organisation->getSlug(),
                'name'    => $organisation->getName(),
                'country' => $this->countryProvider->generateArray($organisation->getCountry()),
                'type'    => $this->typeProvider->generateArray($organisation->getType()),
            ];

            $this->cache->setItem($cacheKey, $organisationData);
        }

        return $organisationData;
    }
}
