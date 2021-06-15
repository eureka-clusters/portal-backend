<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Api\V1\Rest\StatisticsResource;

use Cluster\Service\StatisticsService;
use Admin\Service\UserService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class FacetsListener
 * @package Api\V1\Rest\StatisticsResource
 */
final class FacetsListener extends AbstractResourceListener
{
    private StatisticsService $statisticsService;
    private UserService $userService;

    public function __construct(StatisticsService $statisticsService, UserService $userService)
    {
        $this->statisticsService = $statisticsService;
        $this->userService    = $userService;
    }

    public function fetchAll($data = [])
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        $output = (int)$this->getEvent()->getQueryParams()->get('output');
        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        return $this->statisticsService->generateFacets($user->getFunder(), $arrayFilter, $output);
    }
}
