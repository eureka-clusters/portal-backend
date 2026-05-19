<?php

declare(strict_types=1);

namespace Cluster\Controller\Project;

use Cluster\Entity\Project;
use Cluster\Entity\Project\Area;
use Cluster\Entity\Project\Evaluation;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Version;
use Cluster\Form\ProjectManipulation;
use Cluster\Service\ProjectService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use function count;
use function ksort;
use function sprintf;
use function usort;

final class DetailsController extends AbstractActionController
{
    public function __construct(private readonly ProjectService $projectService)
    {
    }

    public function viewAction(): ViewModel
    {
        $project = $this->findProject();

        if (!$project instanceof Project) {
            return $this->notFoundAction();
        }

        $form = new ProjectManipulation();
        $form->setAttribute(
            key: 'action',
            value: $this->url()->fromRoute(route: 'zfcadmin/project/view', params: ['id' => $project->getId()])
        );

        return new ViewModel(
            variables: [
                'project'           => $project,
                'form'              => $form,
                'navigation'        => $this->buildNavigation(project: $project, currentRoute: 'zfcadmin/project/details/view'),
                'overviewSections'  => $this->buildOverviewSections(project: $project),
                'dependencySummary' => $this->buildDependencySummary(project: $project),
            ]
        );
    }

    public function versionsAction(): ViewModel
    {
        $project = $this->findProject();

        if (!$project instanceof Project) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            variables: [
                'project'    => $project,
                'navigation' => $this->buildNavigation(project: $project, currentRoute: 'zfcadmin/project/details/versions'),
                'groups'     => $this->buildVersionGroups(project: $project),
            ]
        );
    }

    public function areasAction(): ViewModel
    {
        $project = $this->findProject();

        if (!$project instanceof Project) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            variables: [
                'project'    => $project,
                'navigation' => $this->buildNavigation(project: $project, currentRoute: 'zfcadmin/project/details/areas'),
                'groups'     => $this->buildAreaGroups(project: $project),
            ]
        );
    }

    public function partnersAction(): ViewModel
    {
        $project = $this->findProject();

        if (!$project instanceof Project) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            variables: [
                'project'    => $project,
                'navigation' => $this->buildNavigation(project: $project, currentRoute: 'zfcadmin/project/details/partners'),
                'groups'     => $this->buildPartnerGroups(project: $project),
            ]
        );
    }

    public function evaluationsAction(): ViewModel
    {
        $project = $this->findProject();

        if (!$project instanceof Project) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            variables: [
                'project'    => $project,
                'navigation' => $this->buildNavigation(project: $project, currentRoute: 'zfcadmin/project/details/evaluations'),
                'groups'     => $this->buildEvaluationGroups(project: $project),
            ]
        );
    }

    private function findProject(): ?Project
    {
        $project = $this->projectService->find(entity: Project::class, id: (int)$this->params('id'));

        return $project instanceof Project ? $project : null;
    }

    private function buildNavigation(Project $project, string $currentRoute): array
    {
        $params = ['id' => $project->getId()];

        return [
            [
                'label'       => 'Overview',
                'route'       => 'zfcadmin/project/details/view',
                'routeParams' => $params,
                'isActive'    => $currentRoute === 'zfcadmin/project/details/view',
                'count'       => null,
            ],
            [
                'label'       => 'Versions',
                'route'       => 'zfcadmin/project/details/versions',
                'routeParams' => $params,
                'isActive'    => $currentRoute === 'zfcadmin/project/details/versions',
                'count'       => count($project->getVersions()),
            ],
            [
                'label'       => 'Areas',
                'route'       => 'zfcadmin/project/details/areas',
                'routeParams' => $params,
                'isActive'    => $currentRoute === 'zfcadmin/project/details/areas',
                'count'       => count($project->getAreas()),
            ],
            [
                'label'       => 'Partners',
                'route'       => 'zfcadmin/project/details/partners',
                'routeParams' => $params,
                'isActive'    => $currentRoute === 'zfcadmin/project/details/partners',
                'count'       => count($project->getPartners()),
            ],
            [
                'label'       => 'Evaluations',
                'route'       => 'zfcadmin/project/details/evaluations',
                'routeParams' => $params,
                'isActive'    => $currentRoute === 'zfcadmin/project/details/evaluations',
                'count'       => count($project->getEvaluation()),
            ],
        ];
    }

    private function buildOverviewSections(Project $project): array
    {
        $coordinator = $project->getCoordinatorPartner();

        return [
            [
                'title' => 'Project information',
                'items' => [
                    ['label' => 'txt-id', 'value' => (string)$project->getId(), 'translate' => true, 'format' => 'text'],
                    ['label' => 'txt-identifier', 'value' => $project->getIdentifier(), 'translate' => true, 'format' => 'text'],
                    ['label' => 'Project number', 'value' => $project->getNumber(), 'translate' => false, 'format' => 'text'],
                    ['label' => 'txt-name', 'value' => $project->getName(), 'translate' => true, 'format' => 'text'],
                    ['label' => 'Title', 'value' => $project->getTitle(), 'translate' => false, 'format' => 'text'],
                    ['label' => 'txt-primary-cluster', 'value' => $project->getPrimaryCluster()->getName(), 'translate' => true, 'format' => 'text'],
                    ['label' => 'txt-secondary-cluster', 'value' => $project->hasSecondaryCluster() ? $project->getSecondaryCluster()->getName() : null, 'translate' => true, 'format' => 'text'],
                    ['label' => 'txt-programme', 'value' => $project->getProgramme(), 'translate' => true, 'format' => 'text'],
                    ['label' => 'txt-programme-call', 'value' => $project->getProgrammeCall(), 'translate' => true, 'format' => 'text'],
                    ['label' => 'Technical area', 'value' => $project->getTechnicalArea(), 'translate' => false, 'format' => 'text'],
                    ['label' => 'txt-project-status', 'value' => $project->getStatus()->getStatus(), 'translate' => true, 'format' => 'text'],
                    ['label' => 'txt-is-successful', 'value' => $project->isSuccessful(), 'translate' => true, 'format' => 'boolean'],
                    ['label' => 'txt-description', 'value' => $project->getDescription(), 'translate' => true, 'format' => 'text'],
                ],
            ],
            [
                'title' => 'Schedule',
                'items' => [
                    ['label' => 'txt-label-date', 'value' => $project->getLabelDate(), 'translate' => true, 'format' => 'date'],
                    ['label' => 'Cancel date', 'value' => $project->getCancelDate(), 'translate' => false, 'format' => 'date'],
                    ['label' => 'txt-official-start-date', 'value' => $project->getOfficialStartDate(), 'translate' => true, 'format' => 'date'],
                    ['label' => 'txt-official-end-date', 'value' => $project->getOfficialEndDate(), 'translate' => true, 'format' => 'date'],
                    ['label' => 'txt-date-created', 'value' => $project->getDateCreated(), 'translate' => true, 'format' => 'datetime'],
                    ['label' => 'txt-date-updated', 'value' => $project->getDateUpdated(), 'translate' => true, 'format' => 'datetime'],
                ],
            ],
            [
                'title' => 'Contacts',
                'items' => [
                    ['label' => 'Project leader', 'value' => $project->getProjectLeader(), 'translate' => false, 'format' => 'contact'],
                    [
                        'label'     => 'Coordinator',
                        'value'     => null === $coordinator ? null : [
                            'organisation' => $coordinator->getOrganisation()->getName(),
                            'contact'      => $coordinator->getTechnicalContact(),
                        ],
                        'translate' => false,
                        'format'    => 'coordinator',
                    ],
                ],
            ],
            [
                'title' => 'Financial summary',
                'items' => [
                    ['label' => 'txt-project-outline-costs', 'value' => $project->getProjectOutlineCosts(), 'translate' => true, 'format' => 'number'],
                    ['label' => 'txt-project-outline-effort', 'value' => $project->getProjectOutlineEffort(), 'translate' => true, 'format' => 'number'],
                    ['label' => 'txt-full-project-proposal-costs', 'value' => $project->getFullProjectProposalCosts(), 'translate' => true, 'format' => 'number'],
                    ['label' => 'txt-full-project-proposal-effort', 'value' => $project->getFullProjectProposalEffort(), 'translate' => true, 'format' => 'number'],
                    ['label' => 'txt-latest-version-costs', 'value' => $project->getLatestVersionCosts(), 'translate' => true, 'format' => 'number'],
                    ['label' => 'txt-latest-version-effort', 'value' => $project->getLatestVersionEffort(), 'translate' => true, 'format' => 'number'],
                ],
            ],
        ];
    }

    private function buildDependencySummary(Project $project): array
    {
        $versionSummary = [];
        foreach ($project->getVersions() as $version) {
            $group                  = $version->getType()->getDescription();
            $versionSummary[$group] = ($versionSummary[$group] ?? 0) + 1;
        }
        ksort($versionSummary);

        $areaSummary = [];
        foreach ($project->getAreas() as $area) {
            $group               = $area->getType();
            $areaSummary[$group] = ($areaSummary[$group] ?? 0) + 1;
        }
        ksort($areaSummary);

        $activePartners      = 0;
        $coordinatorPartners = 0;
        foreach ($project->getPartners() as $partner) {
            if ($partner->isActive()) {
                ++$activePartners;
            }

            if ($partner->isCoordinator()) {
                ++$coordinatorPartners;
            }
        }

        $fundingEvaluations = 0;
        $versionEvaluations = 0;
        foreach ($project->getEvaluation() as $evaluation) {
            if ($evaluation->isFundingStatus()) {
                ++$fundingEvaluations;
            } else {
                ++$versionEvaluations;
            }
        }

        return [
            [
                'title'       => 'Versions',
                'route'       => 'zfcadmin/project/details/versions',
                'routeParams' => ['id' => $project->getId()],
                'count'       => count($project->getVersions()),
                'lines'       => $this->buildSummaryLines($versionSummary),
            ],
            [
                'title'       => 'Areas',
                'route'       => 'zfcadmin/project/details/areas',
                'routeParams' => ['id' => $project->getId()],
                'count'       => count($project->getAreas()),
                'lines'       => $this->buildSummaryLines($areaSummary),
            ],
            [
                'title'       => 'Partners',
                'route'       => 'zfcadmin/project/details/partners',
                'routeParams' => ['id' => $project->getId()],
                'count'       => count($project->getPartners()),
                'lines'       => [
                    ['label' => 'Active', 'count' => $activePartners],
                    ['label' => 'Coordinators', 'count' => $coordinatorPartners],
                ],
            ],
            [
                'title'       => 'Evaluations',
                'route'       => 'zfcadmin/project/details/evaluations',
                'routeParams' => ['id' => $project->getId()],
                'count'       => count($project->getEvaluation()),
                'lines'       => [
                    ['label' => 'Funding status', 'count' => $fundingEvaluations],
                    ['label' => 'Version linked', 'count' => $versionEvaluations],
                ],
            ],
        ];
    }

    private function buildVersionGroups(Project $project): array
    {
        $versions = $project->getVersions()->toArray();
        usort($versions, static function (Version $left, Version $right): int {
            $leftDate  = $left->getSubmissionDate()?->getTimestamp() ?? 0;
            $rightDate = $right->getSubmissionDate()?->getTimestamp() ?? 0;

            return $rightDate <=> $leftDate ?: $left->getId() <=> $right->getId();
        });

        $groups = [];
        foreach ($versions as $version) {
            $group                    = $version->getType()->getDescription();
            $groups[$group]['title']  ??= $group;
            $groups[$group]['rows'][] = [
                'id'             => (string)$version->getId(),
                'identifier'     => $version->getIdentifier(),
                'status'         => $version->getStatus()->getStatus(),
                'submissionDate' => $version->getSubmissionDate(),
                'reviewDate'     => $version->getReviewDate(),
                'countries'      => $version->getCountries(),
                'costs'          => $version->getCosts(),
                'effort'         => $version->getEffort(),
            ];
        }

        return $groups;
    }

    private function buildAreaGroups(Project $project): array
    {
        $areas = $project->getAreas()->toArray();
        usort($areas, static function (Area $left, Area $right): int {
            return [$left->getType(), $left->getLabel(), $left->getCode()] <=> [$right->getType(), $right->getLabel(), $right->getCode()];
        });

        $groups = [];
        foreach ($areas as $area) {
            $group                    = $area->getType();
            $groups[$group]['title']  ??= $group;
            $groups[$group]['rows'][] = [
                'id'    => (string)$area->getId(),
                'code'  => $area->getCode(),
                'label' => $area->getLabel(),
            ];
        }

        return $groups;
    }

    private function buildPartnerGroups(Project $project): array
    {
        $partners = $project->getPartners()->toArray();
        usort($partners, static function (Partner $left, Partner $right): int {
            return [$right->isCoordinator(), $left->getOrganisation()->getName()] <=> [$left->isCoordinator(), $right->getOrganisation()->getName()];
        });

        $groups = [
            'coordinators' => ['title' => 'Coordinators', 'rows' => []],
            'partners'     => ['title' => 'Partners', 'rows' => []],
        ];

        foreach ($partners as $partner) {
            $row = [
                'id'                        => (string)$partner->getId(),
                'organisation'              => $partner->getOrganisation()->getName(),
                'country'                   => $partner->getOrganisation()->getCountry()->getCountry(),
                'type'                      => $partner->getOrganisation()->getType()->getType(),
                'technicalContact'          => $partner->getTechnicalContact(),
                'isActive'                  => $partner->isActive(),
                'isCoordinator'             => $partner->isCoordinator(),
                'isSelfFunded'              => $partner->isSelfFunded(),
                'projectOutlineCosts'       => $partner->getProjectOutlineCosts(),
                'projectOutlineEffort'      => $partner->getProjectOutlineEffort(),
                'fullProjectProposalCosts'  => $partner->getFullProjectProposalCosts(),
                'fullProjectProposalEffort' => $partner->getFullProjectProposalEffort(),
                'latestVersionCosts'        => $partner->getLatestVersionCosts(),
                'latestVersionEffort'       => $partner->getLatestVersionEffort(),
            ];

            if ($partner->isCoordinator()) {
                $groups['coordinators']['rows'][] = $row;
                continue;
            }

            $groups['partners']['rows'][] = $row;
        }

        return $groups;
    }

    private function buildEvaluationGroups(Project $project): array
    {
        $evaluations = $project->getEvaluation()->toArray();
        usort($evaluations, static function (Evaluation $left, Evaluation $right): int {
            return $right->getDateCreated()->getTimestamp() <=> $left->getDateCreated()->getTimestamp();
        });

        $groups = [
            'funding-status' => ['title' => 'Funding status', 'rows' => []],
        ];

        foreach ($evaluations as $evaluation) {
            $row = [
                'id'          => (string)$evaluation->getId(),
                'country'     => $evaluation->getCountry()->getCountry(),
                'status'      => $evaluation->getStatus()->getStatusEvaluation() ?? $evaluation->getStatus()->getStatus(),
                'user'        => $evaluation->getUser()->parseFullName(),
                'dateCreated' => $evaluation->getDateCreated(),
                'description' => $evaluation->getDescription(),
            ];

            if ($evaluation->isFundingStatus()) {
                $groups['funding-status']['rows'][] = $row;
                continue;
            }

            $version = $evaluation->getProjectVersion();
            $group   = sprintf(
                '%s (%s)',
                $version->getType()->getDescription(),
                $version->getIdentifier()
            );

            $groups[$group]['title']  ??= $group;
            $groups[$group]['rows'][] = $row;
        }

        return $groups;
    }

    /**
     * @param array<string, int> $summary
     * @return array<int, array{label: string, count: int}>
     */
    private function buildSummaryLines(array $summary): array
    {
        $lines = [];
        foreach ($summary as $label => $count) {
            $lines[] = ['label' => $label, 'count' => $count];
        }

        return $lines;
    }
}
