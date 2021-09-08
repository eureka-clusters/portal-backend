<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2020 Jield BV (https://jield.nl)
 */

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Cluster\Provider\ProjectProvider;
use Cluster\Rest\Collection\ProjectCollection;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class ProjectListener
 * @package Api\V1\Rest\ListResource
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

        $projects = $this->projectService->findProjects($user->getFunder());

        return (new ProjectCollection($projects->getQuery()->getResult(), $this->projectProvider))->getItems(
            $params->offset,
            $params->amount ?? 100
        );
    }
}
