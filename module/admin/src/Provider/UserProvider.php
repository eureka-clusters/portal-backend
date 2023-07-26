<?php

declare(strict_types=1);

namespace Admin\Provider;

use Admin\Entity\User;
use Api\Provider\ProviderInterface;
use Cluster\Provider\CountryProvider;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'user',
    description: 'User information',
    content: new OA\JsonContent(ref: '#/components/schemas/user'),
)]
class UserProvider implements ProviderInterface
{
    public function __construct(private readonly CountryProvider $countryProvider)
    {
    }

    #[OA\Schema(
        schema: 'user',
        title: 'User',
        description: 'User information',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'User ID',
                type: 'integer',
                example: 1
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
                property: 'fullName',
                description: 'Full name of the user (and email if empty)',
                type: 'string',
                example: 'John Doe'
            ),
            new OA\Property(
                property: 'email',
                description: 'User email',
                type: 'string',
                example: 'noreply@example.com'
            ),
            new OA\Property(
                property: 'isFunder',
                description: 'Is user a funder',
                type: 'boolean',
                example: true
            ),
            new OA\Property(
                property: 'isEurekaSecretariatStaffMember',
                description: 'Is user a Eureka Secretariat staff member',
                type: 'boolean',
                example: true
            ),
            new OA\Property(
                property: 'funderCountry',
                ref: '#/components/schemas/country'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var User $user */
        $user = $entity;

        return [
            'id'                             => $user->getId(),
            'firstName'                      => $user->getFirstName(),
            'lastName'                       => $user->getLastName(),
            'fullName'                       => $user->parseFullName(),
            'email'                          => $user->getEmail(),
            'isFunder'                       => $user->isFunder(),
            'isEurekaSecretariatStaffMember' => $user->isEurekaSecretariatStaffMember(),
            'funderCountry'                  => $user->isFunder() ? $this->countryProvider->generateArray(
                $user->getFunder()->getCountry()
            ) : null,
        ];
    }
}
