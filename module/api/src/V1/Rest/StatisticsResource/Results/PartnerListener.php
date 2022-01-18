<?php

declare (strict_types=1);

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
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return new Paginator(new ArrayAdapter());
        }

        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        $partnerQueryBuilder = $this->partnerService->getPartners($user->getFunder(), $arrayFilter);

        if (!empty($arrayFilter['year'])) {
            $partnerYears = [];
            //We need to pepare the parnters so we get results per year
            foreach ($partnerQueryBuilder->getQuery()->getResult() as $partner) {
                foreach ($arrayFilter['year'] as $year) {
                    $partnerYears[] = array_merge(
                        $this->partnerProvider->generateArray($partner),
                        $this->partnerProvider->generateYearArray($partner, (int)$year)
                    );
                }
            }

            return new Paginator(new ArrayAdapter($partnerYears));
        }

        $defaultorder = 'asc';
        $defaultSort = 'partner.organisation.name';
        // $defaultSort = 'partner.name';
        $sort = $this->getEvent()->getQueryParams()->get('sort', $defaultSort);
        $order = $this->getEvent()->getQueryParams()->get('order', 'asc');
        $this->applySorting($partnerQueryBuilder, $sort, $order);


        $doctrineORMAdapter = new DoctrineORMAdapter($partnerQueryBuilder);
        $doctrineORMAdapter->setProvider($this->partnerProvider);

        return new Paginator($doctrineORMAdapter);
    }

    public function applySorting($qb, $sort, $order)
    {
        $sortColumn = null;

        switch ($sort) {
            case 'partner.id':
                $sortColumn = 'project_partner.id';
                break;
            case 'partner.project.name':
                $sortColumn = 'project.name';
                $qb->join('project_partner.project', 'project');
                break;
            case 'partner.organisation.name':
                $sortColumn = 'organisation.name';
                $qb->join('project_partner.organisation', 'organisation');
                break;
            case 'partner.organisation.country.country':
                $sortColumn = 'organisation_country.country';
                $qb->join('project_partner.organisation', 'organisation');
                $qb->join('organisation.country', 'organisation_country');
                break;
            case 'partner.organisation.type.type':
                $sortColumn = 'organisation_type.type';
                $qb->join('project_partner.organisation', 'organisation');
                $qb->join('organisation.type', 'organisation_type');
                break;

            //has issues:  Partner has no field or association named latestVersionCosts or latestVersionEffort
            case 'partner.latestVersionCosts':
                $sortColumn = 'project_partner.latestVersionCosts';
                break;
            case 'partner.latestVersionEffort':
                $sortColumn = 'project_partner.latestVersionEffort';
                break;

            // has issues also no field and also no possibility yet to sort for it
            case 'partner.year':
                $sortColumn = 'project_partner.year';
                break;
            case 'partner.latestVersionTotalCostsInYear':
                $sortColumn = 'project_partner.latestVersionTotalCostsInYear';
                break;
            case 'partner.latestVersionTotalEffortInYear':
                $sortColumn = 'project_partner.latestVersionTotalEffortInYear';
                break;
        }

        // var_dump($sort);
        // var_dump($order);
        // var_dump($sortColumn);
        // die();

        if (isset($sortColumn)) {
            $qb->orderBy($sortColumn, $order);
        }
    }
}
