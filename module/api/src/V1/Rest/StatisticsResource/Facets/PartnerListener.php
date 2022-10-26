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
        private readonly PartnerService $partnerService,
        private readonly UserService $userService
    ) {
    }

    public function fetch($id)
    {
        $user = $this->userService->findUserById(id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user) {
            return [
                'countries' => [],
                'organisationTypes' => [],
                'projectStatus' => [],
                'programmeCall' => [],
                'clusters' => [],
                'years' => [],
            ];
        }

        //The filter is a base64 encoded serialised json string
        $filter = base64_decode(string: $id);
        $arrayFilter = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);

        return $this->partnerService->generateFacets(user: $user, filter: $arrayFilter);
    }
}
