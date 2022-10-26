<?php

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity\Cluster;

class ClusterService extends AbstractService
{
    public function findClusterById(int $id): ?Cluster
    {
        return $this->entityManager->find(className: Cluster::class, id: $id);
    }

    public function findClusterByName(string $name): ?Cluster
    {
        return $this->entityManager->getRepository(entityName: Cluster::class)->findOneBy(criteria: ['name' => $name]);
    }

    public function findOrCreateCluster(array $clusterData): Cluster
    {
        $cluster = $this->findClusterByIdentifier(identifier: $clusterData['identifier']);

        //If we cannot find the cluster we create a new one.
        if (null === $cluster) {
            $cluster = new Cluster();
            $cluster->setIdentifier(identifier: $clusterData['identifier']);
        }

        //It will be possible to update the cluster name
        $cluster->setName(name: $clusterData['name']);
        $cluster->setDescription(description: $clusterData['description']);
        $this->save(entity: $cluster);

        return $cluster;
    }

    public function findClusterByIdentifier(string $identifier): ?Cluster
    {
        return $this->entityManager->getRepository(entityName: Cluster::class)->findOneBy(criteria: ['identifier' => $identifier]);
    }
}
