<?php

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity\Cluster;

class ClusterService extends AbstractService
{
    public function findClusterById(int $id): ?Cluster
    {
        return $this->entityManager->find(Cluster::class, $id);
    }

    public function findClusterByName(string $name): ?Cluster
    {
        return $this->entityManager->getRepository(Cluster::class)->findOneBy(['name' => $name]);
    }

    public function findOrCreateCluster(array $clusterData): Cluster
    {
        $cluster = $this->findClusterByIdentifier($clusterData['identifier']);

        //If we cannot find the cluster we create a new one.
        if (null === $cluster) {
            $cluster = new Cluster();
            $cluster->setIdentifier($clusterData['identifier']);
        }

        //It will be possible to update the cluster name
        $cluster->setName($clusterData['name']);
        $cluster->setDescription($clusterData['description']);
        $this->save($cluster);

        return $cluster;
    }

    public function findClusterByIdentifier(string $identifier): ?Cluster
    {
        return $this->entityManager->getRepository(Cluster::class)->findOneBy(['identifier' => $identifier]);
    }
}
