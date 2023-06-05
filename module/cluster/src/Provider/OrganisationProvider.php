<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Organisation;
use Cluster\Provider\Organisation\TypeProvider;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'organisation',
    description: 'Organisation information',
    content: new OA\JsonContent(ref: '#/components/schemas/organisation')
)]
class OrganisationProvider implements ProviderInterface
{
    public function __construct(
        private readonly Redis $cache,
        private readonly CountryProvider $countryProvider,
        private readonly TypeProvider $typeProvider
    ) {
    }

    #[OA\Schema(
        schema: 'organisation',
        title: 'Organisation',
        description: 'Information about an organisation',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Organisation ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'slug',
                description: 'Organisation slug',
                type: 'string',
                example: 'organisation-1'
            ),
            new OA\Property(
                property: 'name',
                description: 'Organisation name',
                type: 'string',
                example: 'Organisation 1'
            ),
            new OA\Property(
                property: 'country',
                ref: '#/components/schemas/country',
                description: 'Country information'
            ),
            new OA\Property(
                property: 'type',
                ref: '#/components/schemas/organisation_type',
                description: 'Organisation type information'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Organisation $organisation */
        $organisation = $entity;

        $cacheKey = $organisation->getResourceId();

        $organisationData = $this->cache->getItem(key: $cacheKey);

        if (!$organisationData) {
            $organisationData = [
                'id'      => $organisation->getId(),
                'slug'    => $organisation->getSlug(),
                'name'    => $organisation->getName(),
                'country' => $this->countryProvider->generateArray(entity: $organisation->getCountry()),
                'type'    => $this->typeProvider->generateArray(entity: $organisation->getType()),
            ];

            $this->cache->setItem(key: $cacheKey, value: $organisationData);
        }

        return $organisationData;
    }
}
