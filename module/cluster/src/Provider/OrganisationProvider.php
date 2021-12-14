<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity\Organisation;
use Cluster\Entity;
use Cluster\Provider\Organisation\TypeProvider;
use Doctrine\Common\Cache\RedisCache;

class OrganisationProvider
{
    public function __construct(private RedisCache $redisCache, private CountryProvider $countryProvider, private TypeProvider $typeProvider)
    {
    }

    public function generateArray(Organisation $organisation): array
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
