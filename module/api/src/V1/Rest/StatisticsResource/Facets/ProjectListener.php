<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Facets;

use Admin\Service\UserService;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;

final class ProjectListener extends AbstractResourceListener
{
    private ProjectService $projectService;
    private UserService $userService;

    public function __construct(ProjectService $projectService, UserService $userService)
    {
        $this->projectService = $projectService;
        $this->userService    = $userService;
    }

    public function fetchAll($data = [])
    {
        $user = $this->userService->findUserById((int) $this->getIdentity()?->getName());

        if (null === $user || ! $user->isFunder()) {
            return [];
        }

        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        //Make sure you wrap the response in an array!!
        return [$this->projectService->generateFacets($user->getFunder(), $arrayFilter)];
    }
}
