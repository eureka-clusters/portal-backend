<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Api\V1\Rest\StatisticsResource\Results;

use Admin\Service\UserService;
use Cluster\Provider\ProjectProvider;
use Cluster\Rest\Collection\ProjectCollection;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 *
 */
final class ProjectListener extends AbstractResourceListener
{
    private ProjectService  $projectService;
    private UserService     $userService;
    private ProjectProvider $projectProvider;

    public function __construct(
        ProjectService $projectService,
        UserService $userService,
        ProjectProvider $projectProvider
    ) {
        $this->projectService  = $projectService;
        $this->userService     = $userService;
        $this->projectProvider = $projectProvider;
    }

    public function fetchAll($params = [])
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        $projects = $this->projectService->getProjects($user->getFunder(), $arrayFilter);

        return (new ProjectCollection($projects, $this->projectProvider))->getItems(
            $params->offset,
            $params->amount ?? 100
        );
    }
}