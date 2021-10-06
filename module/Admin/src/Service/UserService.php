<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity\User;
use Application\Service\AbstractService;
use Application\ValueObject\OAuth2\GenericUser;
use Cluster\Entity\Cluster;
use Cluster\Entity\Country;
use Cluster\Entity\Funder;

/**
 * Class UserService
 *
 * @package Admin\Service
 */
class UserService extends AbstractService
{
    public function findUserById(int $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }

    public function findOrCreateUserFromGenericUser(GenericUser $genericUser, array $allowedClusters = []): User
    {
        //Try to see if we already have the user
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            [
                'email' => $genericUser->getEmail()
            ]
        );

        if (null === $user) {
            $user = new User();
            $user->setEmail($genericUser->getEmail());
        }

        $user->setFirstName($genericUser->getFirstName());
        $user->setLastName($genericUser->getLastName());

        $this->save($user);

        //Delete the funder object when the user is not a funder
        if (!$genericUser->isFunder() && $user->isFunder()) {
            $this->delete($user->getFunder());
        }

        if ($genericUser->isFunder()) {
            //Handle the funder

            $funder = $user->getFunder();

            $country = $this->entityManager->getRepository(Country::class)->findOneBy(
                [
                    'cd' => $genericUser->getFunderCountry(),
                ]
            );
            if (null === $country) {
                throw new \Exception(
                    sprintf('Error Country with Alpha 2 code "%s" not found', $genericUser->getFunderCountry()),
                    1
                );
            }

            if (null === $funder) {
                $funder = new Funder();
                $funder->setUser($user);
                $funder->setCountry($country);
            }
            $this->save($funder);

            $this->updateClusterPermissions($funder, $genericUser, $allowedClusters);
        }

        return $user;
    }

    protected function updateClusterPermissions(Funder $funder, GenericUser $genericUser, array $allowedClusters = [])
    {
        // get the ClusterPermissions from generic User
        $clusterPermissions = $genericUser->getClusterPermissions();

        $funderClusters = $funder->getClusters();

        // map clusters to each identifier name
        $linkedIdentifierArray = $funderClusters->map(
            fn(Cluster $cluster) => $cluster->getIdentifier()
        )->toArray();

        // filter by allowedClusters of this oauth provider to only remove clusters which are changeable.
        $linkedIdentifierArray = array_intersect($linkedIdentifierArray, $allowedClusters);

        // get the clusters to add
        $identifiersToAdd = array_values(array_diff($clusterPermissions, $linkedIdentifierArray));

        // add the clusters which permission was added
        foreach ($identifiersToAdd as $clusterIdentifier) {
            $cluster = $this->entityManager->getRepository(Cluster::class)->findOneBy(
                [
                    'identifier' => $clusterIdentifier,
                ]
            );
            if ((null !== $cluster) && !$funder->getClusters()->contains($cluster)) {
                $funder->getClusters()->add($cluster);
            }
        }

        // which clusters should be removed given by its identifier
        $identifiersToRemove = array_values(array_diff($linkedIdentifierArray, $clusterPermissions));

        // remove the clusters which permission was revoked
        foreach ($identifiersToRemove as $clusterIdentifier) {
            $cluster = $this->entityManager->getRepository(Cluster::class)->findOneBy(
                [
                    'identifier' => $clusterIdentifier,
                ]
            );
            if ((null !== $cluster) && $funder->getClusters()->contains($cluster)) {
                $funder->getClusters()->removeElement($cluster);
            }
        }

        $this->save($funder);
    }
}
