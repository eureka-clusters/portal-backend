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
 * Class CountryService
 * @package Country\Service
 */
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
