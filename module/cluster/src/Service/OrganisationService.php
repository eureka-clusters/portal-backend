<?php

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity\Country;
use Cluster\Entity\Funder;
use Cluster\Entity\Organisation;
use Cluster\Entity\Organisation\Type;
use Cluster\Repository\OrganisationRepository;
use Doctrine\ORM\QueryBuilder;

class OrganisationService extends AbstractService
{
    public function findOrganisationById(int $id): ?Organisation
    {
        return $this->entityManager->getRepository(Organisation::class)->find($id);
    }

    public function findOrganisationBySlug(string $slug): ?Organisation
    {
        return $this->entityManager->getRepository(Organisation::class)->findOneBy(['slug' => $slug]);
    }

    public function searchOrganisations(Funder $funder, string $query, int $limit): array
    {
        /** @var OrganisationRepository $repository */
        $repository = $this->entityManager->getRepository(Organisation::class);

        return $repository->searchOrganisations($funder, $query, $limit)->getQuery()->getResult();
    }

    public function findOrCreateOrganisationType(string $typeName): Type
    {
        $type = $this->entityManager->getRepository(Type::class)
            ->findOneBy(['type' => $typeName]);

        if (null === $type) {
            $type = new Type();
            $type->setType($typeName);
            $this->save($type);
        }

        return $type;
    }

    public function getOrganisations(
        array $filter,
        string $sort = 'organisation.name',
        string $order = 'asc'
    ): QueryBuilder {
        return $this->entityManager->getRepository(Organisation::class)->getOrganisationsByFilter(
            $filter,
            $sort,
            $order
        );
    }

    public function findOrCreateOrganisation(
        string $name,
        Country $country,
        Type $type
    ): Organisation {
        $organisation = $this->entityManager->getRepository(Organisation::class)
            ->findOneBy(['name' => $name, 'country' => $country, 'type' => $type]);

        //If we cannot find the project we create a new one. Only set the identifier as we will later overwrite/update the properties
        if (null === $organisation) {
            $organisation = new Organisation();
            $organisation->setName($name);
            $organisation->setCountry($country);
            $organisation->setType($type);

            $this->save($organisation);
        }

        return $organisation;
    }
}
