<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity;

/**
 *
 */
class OrganisationService extends AbstractService
{
    public function findOrganisationById(int $id): ?Entity\Organisation
    {
        return $this->entityManager->getRepository(Entity\Organisation::class)->find($id);
    }

    public function findOrganisationBySlug(string $slug): ?Entity\Organisation
    {
        return $this->entityManager->getRepository(Entity\Organisation::class)->findOneBy(['slug' => $slug]);
    }

    public function findOrCreateOrganisationType(string $typeName): Entity\Organisation\Type
    {
        $type = $this->entityManager->getRepository(Entity\Organisation\Type::class)
            ->findOneBy(['type' => $typeName]);

        if (null === $type) {
            $type = new Entity\Organisation\Type();
            $type->setType($typeName);
            $this->save($type);
        }

        return $type;
    }

    public function getOrganisations(array $filter): array
    {
        return $this->entityManager->getRepository(Entity\Organisation::class)->getOrganisationsByFilter($filter);
    }

    public function findOrCreateOrganisation(
        string $name,
        Entity\Country $country,
        Entity\Organisation\Type $type
    ): Entity\Organisation {
        $organisation = $this->entityManager->getRepository(Entity\Organisation::class)
            ->findOneBy(['name' => $name, 'country' => $country, 'type' => $type]);

        //If we cannot find the project we create a new one. Only set the identifier as we will later overwrite/update the properties
        if (null === $organisation) {
            $organisation = new Entity\Organisation();
            $organisation->setName($name);
            $organisation->setCountry($country);
            $organisation->setType($type);

            $this->save($organisation);
        }

        return $organisation;
    }
}
