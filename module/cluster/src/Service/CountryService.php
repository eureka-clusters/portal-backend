<?php

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity\Country;

class CountryService extends AbstractService
{
    public function findCountryById(int $id): ?Country
    {
        return $this->entityManager->find(Country::class, $id);
    }

    public function findCountryByCd(string $cd): ?Country
    {
        return $this->entityManager->getRepository(Country::class)->findOneBy(['cd' => $cd]);
    }
}
