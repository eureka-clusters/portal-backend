<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Facets;

use Admin\Service\UserService;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;

use function base64_decode;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private PartnerService $partnerService,
        private UserService $userService
    ) {
    }

    public function fetch($id)
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [
                'countries'         => [],
                'organisationTypes' => [],
                'projectStatus'     => [],
                'clusters'   => [],
                'years'             => [],
            ];
        }

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($id);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        return $this->partnerService->generateFacets($user->getFunder(), $arrayFilter);
    }
}
