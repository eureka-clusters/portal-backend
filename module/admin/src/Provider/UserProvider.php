<?php

declare(strict_types=1);

namespace Admin\Provider;

use Admin\Entity\User;
use Cluster\Entity\Cluster;

use function array_merge;

class UserProvider
{
    public function generateArray(User $user): array
    {
        return array_merge(
            [
                'id' => $user->getId(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'email' => $user->getEmail(),
                'is_funder' => $user->isFunder(),
                'is_eureka_secretariat_staff_member' => $user->isEurekaSecretariatStaffMember(),
                'funder_country' => $user->getFunder()?->getCountry()->getCountry(),
                'funder_country_cd' => $user->getFunder()?->getCountry()->getCd(),
                'funder_clusters' => $user->getFunder()?->getClusters()->map(
                    fn(Cluster $cluster) => $cluster->getIdentifier()
                )->toArray(),
            ]
        );
    }
}
