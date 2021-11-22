<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Facets;

use Admin\Service\UserService;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

use function base64_decode;
use function json_decode;

use const JSON_THROW_ON_ERROR;

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
        $user = $this->userService->findUserById((int) $this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || ! $user->isFunder()) {
            return [];
        }

        $output        = (int) $this->getEvent()->getQueryParams()->get('output');
        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        return $this->projectService->generateFacets($user->getFunder(), $arrayFilter);
    }
}
