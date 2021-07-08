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

    public function findOrCreateUserFromGenericUser(GenericUser $genericUser): User
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

            // create the cluster entries depending on the Cluster Permissions
            $clusterPermissions = $genericUser->getClusterPermissions();

            //Johan: We need to have something in case a permission is removed
            foreach ($clusterPermissions as $clusterIdentifier) {
                $cluster = $this->entityManager->getRepository(Cluster::class)->findOneBy(
                    [
                        'identifier' => $clusterIdentifier,
                    ]
                );
                if ((null !== $cluster) && !$funder->getClusters()->contains($cluster)) {
                    $funder->getClusters()->add($cluster);
                }
            }
            $this->save($funder);
        }

        return $user;
    }

    // function from the clusterService.
    public function findContactById(int $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }
}
