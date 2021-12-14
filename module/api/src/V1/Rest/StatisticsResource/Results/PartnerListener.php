<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Results;

use Admin\Service\UserService;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Rest\Collection\PartnerCollection;
use Cluster\Rest\Collection\PartnerYearCollection;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Adapter\ArrayAdapter;

use function base64_decode;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private PartnerService $partnerService,
        private UserService $userService,
        private PartnerProvider $partnerProvider
    ) {
    }

    public function fetchAll($params = [])
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getName());

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        $partners = $this->partnerService->getPartners($user->getFunder(), $arrayFilter);

        if (isset($arrayFilter['year'])) {
            $partnerYears = [];
            //We need to pepare the parnters so we get results per year
            foreach ($partners as $partner) {
                foreach ($arrayFilter['year'] as $year) {
                    $partnerYears[] = array_merge(
                        $this->partnerProvider->generateArray($partner),
                        $this->partnerProvider->generateYearArray($partner, (int) $year)
                    );
                }
            }

            return (new ArrayAdapter($partnerYears))->getItems(
                $params->offset,
                $params->amount ?? 100
            );
        }


        return (new PartnerCollection($partners, $this->partnerProvider))->getItems(
            $params->offset,
            $params->amount ?? 100
        );
    }
}
