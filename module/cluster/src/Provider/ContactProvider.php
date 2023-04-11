<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'contact',
    description: 'Contact information',
    content: new OA\JsonContent(ref: '#/components/schemas/contact')
)]
class ContactProvider implements ProviderInterface
{
    #[OA\Schema(
        schema: 'contact',
        title: 'Project contact information',
        description: 'All information about a project contact',
        properties: [
            new OA\Property(
                property: 'fullName',
                description: 'User full name',
                type: 'string',
                example: 'John Doe'
            ),
            new OA\Property(
                property: 'firstName',
                description: 'User first name',
                type: 'string',
                example: 'John'
            ),
            new OA\Property(
                property: 'lastName',
                description: 'User last name',
                type: 'string',
                example: 'Doe'
            ),
            new OA\Property(
                property: 'email',
                description: 'User email',
                type: 'string',
                example: 'noreply@example.com'
            ),
            new OA\Property(
                property: 'address',
                description: 'User address',
                properties: [
                    new OA\Property(
                        property: 'address',
                        description: 'User address',
                        type: 'string',
                        example: 'Example street 1'
                    ),
                    new OA\Property(
                        property: 'city',
                        description: 'User city',
                        type: 'string',
                        example: 'Example city'
                    ),
                    new OA\Property(
                        property: 'zip',
                        description: 'User zip code',
                        type: 'string',
                        example: '12345'
                    ),
                    new OA\Property(
                        property: 'country',
                        description: 'User country',
                        type: 'string',
                        example: 'BE'
                    ),
                ],
                type: 'object'
            ),

        ]
    )]
    public function generateArray($entity): array
    {
        unset($entity['id']);
        unset($entity['cluster']);
        unset($entity['isFunder']);
        unset($entity['isEurekaSecretariatStaffMember']);
        unset($entity['funderCountry']);

        return $entity;
    }
}
