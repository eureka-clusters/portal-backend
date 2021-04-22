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
use Contact\Service\ContactService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class ResultsListener
 * @package Api\V1\Rest\StatisticsResource
 */
final class ResultsListener extends AbstractResourceListener
{
    private StatisticsService $statisticsService;
    private ContactService $contactService;

    public function __construct(StatisticsService $statisticsService, ContactService $contactService)
    {
        $this->statisticsService = $statisticsService;
        $this->contactService    = $contactService;
    }

    public function fetchAll($data = [])
    {
        $contact = $this->contactService->findContactById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $contact || !$contact->isFunder()) {
            return [];
        }

        $output = (int)$this->getEvent()->getQueryParams()->get('output');
        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        return $this->statisticsService->getResults($contact->getFunder(), $arrayFilter, $output);

    }
}
