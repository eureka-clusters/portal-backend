<?php

declare (strict_types = 1);

namespace Api\V1\Rest\StatisticsResource\Results;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

use function base64_decode;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private PartnerService $partnerService,
        private UserService $userService,
        private PartnerProvider $partnerProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getName());


        if (null === $user || !$user->isFunder()) {
            return new Paginator(new ArrayAdapter());
        }

        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        $partnerQueryBuilder = $this->partnerService->getPartners($user->getFunder(), $arrayFilter);

        //@johan: how could this be done?
        if (!empty($arrayFilter['year'])) {
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
                $params->amount?? 100
            );
        }

        $doctrineORMAdapter = new DoctrineORMAdapter($partnerQueryBuilder);
        $doctrineORMAdapter->setProvider($this->partnerProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
