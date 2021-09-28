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
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * 
 */
final class ProjectListener extends AbstractResourceListener
{
    private ProjectService $ProjectService;
    private UserService       $userService;

    public function __construct(ProjectService $ProjectService, UserService $userService)
    {
        $this->ProjectService = $ProjectService;
        $this->userService       = $userService;
    }

    public function fetchAll($data = [])
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        $output        = (int)$this->getEvent()->getQueryParams()->get('output');
        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        return $this->ProjectService->getProjects($user->getFunder(), $arrayFilter);
    }
}
