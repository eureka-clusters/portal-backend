<?php

declare(strict_types=1);

namespace Admin\Provider;

use Admin\Entity\User;
use Admin\Entity;
use Cluster\Entity\Cluster;

use function array_merge;

class UserProvider
{
    public function generateArray(User $user): array
    {
        return array_merge(
            [
                'id'              => $user->getId(),
                'first_name'      => $user->getFirstName(),
                'last_name'       => $user->getLastName(),
                'email'           => $user->getEmail(),
                'is_funder'       => $user->isFunder(),
                'funder_country'  => $user->isFunder() ? $user->getFunder()->getCountry()->getCountry() : null,
                'funder_country_cd'  => $user->isFunder() ? $user->getFunder()->getCountry()->getCd() : null,
                'funder_clusters' => $user->isFunder() ? $user->getFunder()->getClusters()->map(
                    fn (Cluster $cluster) => $cluster->getIdentifier()
                )->toArray() : null,
            ]
        );
    }
}
