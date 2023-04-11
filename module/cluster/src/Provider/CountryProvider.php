<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Country;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'country',
    description: 'Country information',
    content: new OA\JsonContent(ref: '#/components/schemas/country')
)]
class CountryProvider implements ProviderInterface
{
    public function __construct(private readonly Redis $cache)
    {
    }

    #[OA\Schema(
        schema: 'country',
        title: 'Country',
        description: 'Information about a country',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Country ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'country',
                description: 'Country name',
                type: 'string',
                example: 'France'
            ),
            new OA\Property(
                property: 'cd',
                description: 'Country CD',
                type: 'string',
                example: 'FR'
            ),
            new OA\Property(
                property: 'iso3',
                description: 'Country ISO3',
                type: 'string',
                example: 'FRA'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Country $country */
        $country = $entity;

        $cacheKey = $country->getResourceId();

        $countryData = $this->cache->getItem(key: $cacheKey);

        if (!$countryData) {
            $countryData = [
                'id'      => $country->getId(),
                'country' => $country->getCountry(),
                'cd'      => $country->getCd(),
                'iso3'    => $country->getIso3(),
            ];

            $this->cache->setItem(key: $cacheKey, value: $countryData);
        }

        return $countryData;
    }
}
