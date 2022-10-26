<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Facets;

use Admin\Service\UserService;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
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
                'programmeCalls' => [],
                'clusters' => [],
            ];
        }

        //The filter is a base64 encoded serialised json string
        $filter = base64_decode(string: $id);
        $arrayFilter = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);

        //Make sure you wrap the response in an array!!
        return $this->projectService->generateFacets(user: $user, filter: $arrayFilter);
    }
}
