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

        $cluster = $this->entityManager->getRepository(Cluster::class)->findOneBy(
            [
                'identifier' => $genericUser->getCluster(),
            ]
        );

        if (null === $user) {
            $user = new User();
            $user->setEmail($genericUser->getEmail());
        }

        $user->setFirstName($genericUser->getFirstName());
        $user->setLastName($genericUser->getLastName());

        $this->entityManager->persist($user);


        if ($genericUser->isFunder()) {
            //Handle the funder

            $funder = $user->getFunder();

            $country = $this->entityManager->getRepository(Country::class)->findOneBy(
                [
                    'iso3' => $genericUser->getFunderCountry(),
                ]
            );

            if (null === $funder) {
                $funder = new Funder();
                $funder->setUser($user);
                $funder->setCountry($country);
            }
            $this->save($funder);

            //Create an entry in the clusterFinder (DOES NOT WORK YET)
//            if (!$funder->getClusters()->contains($cluster))
//            {
//                $funder->getClusters()->add($cluster);
//            }
//
//            $this->save($funder);
//
//            // @Johan
//            // no entry in cluster_funder_cluster table?
//            // my guess ist because cluster isn't an ArrayCollection?
//            //$funder->setClusters($cluster);
//
//            // with addCluster it works? Why ?
//            $funder->addCluster($cluster);
//
//            $this->entityManager->persist($funder);
//
//            $this->entityManager->flush();
        }

        return $user;
    }

    // function from the clusterService.
    public function findContactById(int $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }
}
