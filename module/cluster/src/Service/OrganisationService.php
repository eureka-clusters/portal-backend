<?php

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity\Country;
use Cluster\Entity\Organisation;
use Cluster\Entity\Organisation\Type;
use Cluster\Repository\OrganisationRepository;
use Doctrine\ORM\QueryBuilder;
use Jield\Search\ValueObject\SearchFormResult;

class OrganisationService extends AbstractService
{
    public function findOrganisationById(int $id): ?Organisation
    {
        return $this->entityManager->getRepository(entityName: Organisation::class)->find(id: $id);
    }

    public function findOrganisationBySlug(string $slug): ?Organisation
    {
        return $this->entityManager->getRepository(entityName: Organisation::class)->findOneBy(
            criteria: ['slug' => $slug]
        );
    }

    public function searchOrganisations(?string $query, int $limit): array
    {
        /** @var OrganisationRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Organisation::class);

        return $repository->searchOrganisations(query: $query, limit: $limit)->getQuery()->getResult();
    }

    public function findOrCreateOrganisationType(string $typeName): Type
    {
        $type = $this->entityManager->getRepository(entityName: Type::class)
            ->findOneBy(criteria: ['type' => $typeName]);

        if (null === $type) {
            $type = new Type();
            $type->setType(type: $typeName);
            $this->save(entity: $type);
        }

        return $type;
    }

    public function getOrganisations(SearchFormResult $searchFormResult): QueryBuilder
    {
        /** @var OrganisationRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Organisation::class);

        return $repository->getOrganisationsByFilter(searchFormResult: $searchFormResult);
    }

    public function findOrCreateOrganisation(
        string  $name,
        Country $country,
        Type    $type,
        ?string $vatNumber = null,
        ?string $companyRegistrationNumber = null,
        ?string $companyRegistrationAuthority = null
    ): Organisation
    {
        $organisation = null;
        if (null !== $vatNumber) {
            //First we try to find the organisation by the VAT number
            $organisation = $this->entityManager->getRepository(entityName: Organisation::class)
                ->findOneBy(criteria: ['vatNumber' => $vatNumber]);
        }

        if ($organisation instanceof Organisation) {
            return $organisation;
        }

        //Then we try to find the organisation by the company registration number and authority
        if (null !== $companyRegistrationNumber && null !== $companyRegistrationAuthority) {
            $organisation = $this->entityManager->getRepository(entityName: Organisation::class)
                ->findOneBy(criteria: [
                    'companyRegistrationNumber'    => $companyRegistrationNumber,
                    'companyRegistrationAuthority' => $companyRegistrationAuthority
                ]);

            if ($organisation instanceof Organisation) {
                return $organisation;
            }
        }

        $organisation = $this->entityManager->getRepository(entityName: Organisation::class)
            ->findOneBy(criteria: ['name' => $name, 'country' => $country, 'type' => $type]);

        //If we cannot find the project we create a new one. Only set the identifier as we will later overwrite/update the properties
        if (null === $organisation) {
            $organisation = new Organisation();
            $organisation->setName(name: $name);
            $organisation->setCountry(country: $country);
            $organisation->setType(type: $type);

            $this->save(entity: $organisation);
        }

        return $organisation;
    }
}
