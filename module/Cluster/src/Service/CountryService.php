<?php

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity;

class CountryService extends AbstractService
{
    public function findCountryById(int $id): ?Entity\Country
    {
        return $this->entityManager->find(Entity\Country::class, $id);
    }

    public function findCountryByCd(string $cd): ?Entity\Country
    {
        return $this->entityManager->getRepository(Entity\Country::class)->findOneBy(['cd' => $cd]);
    }
}
