<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Facets;

use Admin\Service\UserService;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(private ProjectService $projectService, private UserService $userService)
    {
    }

    public function fetch($id)
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [
                'countries'         => [],
                'organisationTypes' => [],
                'projectStatus'     => [],
                'primaryClusters'   => [],
            ];
        }

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($id);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        //Make sure you wrap the response in an array!!
        return $this->projectService->generateFacets($user->getFunder(), $arrayFilter);
    }
}
