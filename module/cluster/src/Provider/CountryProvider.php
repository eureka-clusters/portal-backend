<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Country;
use Laminas\Cache\Storage\Adapter\Redis;

class CountryProvider implements ProviderInterface
{
    public function __construct(private Redis $cache)
    {
    }

    /**
     * @param Country $country
     * @return array
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function generateArray($country): array
    {
        $cacheKey = $country->getResourceId();

        $countryData = $this->cache->getItem($cacheKey);

        if (!$countryData) {
            $countryData = [
                'id'      => $country->getId(),
                'country' => $country->getCountry(),
                'cd'      => $country->getCd(),
                'iso3'    => $country->getIso3(),
            ];

            $this->cache->setItem($cacheKey, $countryData);
        }

        return $countryData;
    }
}
