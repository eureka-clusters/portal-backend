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

    public function findClusterByName(string $name): ?Entity\Cluster
    {
        return $this->entityManager->getRepository(Entity\Cluster::class)->findOneBy(['name' => $name]);
    }

    public function findClusterByIdentifier(string $identifier): ?Entity\Cluster
    {
        return $this->entityManager->getRepository(Entity\Cluster::class)->findOneBy(['identifier' => Entity\Cluster::getSafeIdentifierFromName($identifier)]);
    }

    public function findOrCreateCluster(string $name): Entity\Cluster
    {
        $identifier = Entity\Cluster::getSafeIdentifierFromName($name);
        $cluster    = $this->findClusterByIdentifier($identifier);

        //If we cannot find the cluster we create a new one.
        if (null === $cluster) {
            $cluster = new Entity\Cluster();
            $cluster->setName($name);
            // $description = $name . ' auto created cluster entry';
            // $cluster->setDescription($description);
            $cluster->setIdentifier($identifier);
            $this->save($cluster);
        }
        return $cluster;
    }
}
