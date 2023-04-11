<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'search_result',
    description: 'Search result response',
    content: new OA\JsonContent(ref: '#/components/schemas/search_result')
)]
class SearchResultProvider implements ProviderInterface
{
    #[OA\Schema(
        schema: 'search_result',
        title: 'Search result response',
        description: 'Response given when searching for a resource',
        properties: [
            new OA\Property(
                property: 'type',
                description: 'Search result type',
                type: 'string',
                example: 'project'
            ),
            new OA\Property(
                property: 'slug',
                description: 'Slug/Docref of the resource',
                type: 'string',
                example: 'company-name'
            ),
            new OA\Property(
                property: 'name',
                description: 'Name of the resource',
                type: 'string',
                example: 'Company name'
            ),
            new OA\Property(
                property: 'title',
                description: 'Title of the resource, for example for a project',
                type: 'string',
                example: 'Project title',
                nullable: true
            ),
            new OA\Property(
                property: 'description',
                description: 'description of the resource, for example for a project',
                type: 'string',
                example: 'Project description',
                nullable: true
            ),
            new OA\Property(
                property: 'organisationType',
                description: 'In case of an organisation, the type of the organisation',
                type: 'string',
                example: 'Large industry',
                nullable: true
            ),
            new OA\Property(
                property: 'country',
                description: 'In case of an organisation, the country of the organisation',
                type: 'string',
                example: 'France',
                nullable: true
            ),
            new OA\Property(
                property: 'score',
                description: 'The score of the search result',
                type: 'number',
                example: 1.00,
                nullable: true
            ),

        ]
    )]
    public function generateArray($entity): array
    {
        return $entity->toArray();
    }
}
