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
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Rest\Collection\PartnerCollection;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 *
 */
final class PartnerListener extends AbstractResourceListener
{
    private PartnerService  $partnerService;
    private UserService     $userService;
    private PartnerProvider $partnerProvider;

    public function __construct(
        PartnerService $partnerService,
        UserService $userService,
        PartnerProvider $partnerProvider
    ) {
        $this->partnerService = $partnerService;
        $this->userService = $userService;
        $this->partnerProvider = $partnerProvider;
    }

    public function fetchAll($params = [])
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

        $partners = $this->partnerService->getPartners($user->getFunder(), $arrayFilter);

        return (new PartnerCollection($partners, $this->partnerProvider))->getItems(
            $params->offset,
            $params->amount ?? 100
        );
    }
}
