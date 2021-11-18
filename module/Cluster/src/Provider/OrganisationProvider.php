<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity;
use Cluster\Provider\Organisation\TypeProvider;
use Doctrine\Common\Cache\RedisCache;

class OrganisationProvider
{
    private RedisCache $redisCache;
    private CountryProvider $countryProvider;
    private TypeProvider $typeProvider;

    public function __construct(RedisCache $redisCache, CountryProvider $countryProvider, TypeProvider $typeProvider)
    {
        $this->redisCache      = $redisCache;
        $this->countryProvider = $countryProvider;
        $this->typeProvider    = $typeProvider;
    }

    public function generateArray(Entity\Organisation $organisation): array
    {
        $cacheKey = $organisation->getResourceId();

        $organisationData = $this->redisCache->fetch($cacheKey);

        if (! $organisationData) {
            $organisationData = [
                'id'      => $organisation->getId(),
                'slug'    => $organisation->getSlug(),
                'name'    => $organisation->getName(),
                'country' => $this->countryProvider->generateArray($organisation->getCountry()),
                'type'    => $this->typeProvider->generateArray($organisation->getType()),
            ];

            $this->redisCache->save($cacheKey, $organisationData);
        }

        return $organisationData;
    }
}
