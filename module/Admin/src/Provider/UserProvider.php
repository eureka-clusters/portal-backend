<?php

/**
 * Jield BV All rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2020 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Admin\Provider;

use Admin\Entity;
use Cluster\Entity\Cluster;

/**
 * Class UserProvider
 * @package User\Provider
 */
class UserProvider
{
    public function generateArray(Entity\User $user): array
    {
        return array_merge(
            [
                'id'              => $user->getId(),
                'first_name'      => $user->getFirstName(),
                'last_name'       => $user->getLastName(),
                'email'           => $user->getEmail(),
                'is_funder'       => $user->isFunder(),
                'funder_country'  => $user->isFunder() ? $user->getFunder()->getCountry()->getCd() : null,
                'funder_clusters' => $user->isFunder() ? $user->getFunder()->getClusters()->map(
                    fn (Cluster $cluster) => $cluster->getIdentifier()
                )->toArray() : null,
            ]
        );
    }
}
