<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity;
use Doctrine\Common\Cache\RedisCache;

class CountryProvider
{
    private RedisCache $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function generateArray(Entity\Country $country): array
    {
        $cacheKey = $country->getResourceId();

        $countryData = $this->redisCache->fetch($cacheKey);

        if (! $countryData) {
            $countryData = [
                'id'      => $country->getId(),
                'country' => $country->getCountry(),
                'cd'      => $country->getCd(),
                'iso3'    => $country->getIso3(),
            ];

            $this->redisCache->save($cacheKey, $countryData);
        }

        return $countryData;
    }
}
