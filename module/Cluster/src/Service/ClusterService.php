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
 * Class ClusterService
 * @package Cluster\Service
 */
class ClusterService extends AbstractService
{
    public function findClusterById(int $id): ?Entity\Cluster
    {
        return $this->entityManager->find(Entity\Cluster::class, $id);
    }
}