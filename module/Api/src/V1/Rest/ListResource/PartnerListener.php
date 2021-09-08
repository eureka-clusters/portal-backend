<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2020 Jield BV (https://jield.nl)
 */

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Cluster\Provider\PartnerProvider;
use Cluster\Rest\Collection\PartnerCollection;
use Cluster\Service\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class ProjectListener
 * @package Api\V1\Rest\ListResource
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
        $this->partnerService  = $partnerService;
        $this->userService     = $userService;
        $this->partnerProvider = $partnerProvider;
    }

    public function fetchAll($params = [])
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        $partnerQueryBuilder = $this->partnerService->findPartners($user->getFunder());

//        switch (true) {
//            case isset($params->call):
//                /** @var Call $call */
//                $call = $this->callService->findCallById((int)$params->call);
//
//                if (null === $call) {
//                    return [];
//                }
//
//                $partners = $this->projectService->findProjectsByCall($call);
//                break;
//            default:
//        }

        return (new PartnerCollection($partnerQueryBuilder->getQuery()->getResult(), $this->partnerProvider))->getItems(
            $params->offset,
            $params->amount ?? 100
        );
    }
}
