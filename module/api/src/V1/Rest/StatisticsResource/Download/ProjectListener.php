<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Cluster\Entity\Project;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Json\Json;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use function base64_decode;
use function base64_encode;
use function ob_get_clean;
use function ob_start;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private ProjectService $projectService,
        private UserService $userService,
        private TranslatorInterface $translator,
        private ProjectProvider $projectProvider
    ) {
    }

    public function fetch($exportType = 'Xlsx'): array
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        //The filter is a base64 encoded serialised json string
        $filter = $this->getEvent()->getQueryParams()?->get('filter');
        $filter      = base64_decode($filter);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        $defaultorder = 'asc';
        $defaultSort = 'project.name';
        $sort = $this->getEvent()->getQueryParams()?->get('sort', $defaultSort);
        $order = $this->getEvent()->getQueryParams()?->get('order', 'asc');

        $projectQueryBuilder = $this->projectService->getProjects($user->getFunder(), $arrayFilter, $sort, $order);

        $projects = $projectQueryBuilder->getQuery()->getResult();

        $results = [];
        /** @var Project $project */
        foreach ($projects as $project) {
            $results[] = $this->projectProvider->generateArray($project);
        }

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getProperties()->setTitle('Statistics');
        $partnerSheet = $spreadSheet->getActiveSheet();

        $partnerSheet->setTitle($this->translator->translate('txt-projects'));

        $row    = 1;
        $column = 'A';
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-project-number'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-project-name'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-primary-cluster'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-secondary-cluster'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-official-start-date'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-official-end-date'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-duration-(months)'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-project-status'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-total-costs'));
        $partnerSheet->setCellValue($column . $row, $this->translator->translate('txt-total-effort'));

        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $partnerSheet->getCell($column++ . $row)->setValue($result['number']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['name']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['primaryCluster']['name'] ?? null);
            $partnerSheet->getCell($column++ . $row)->setValue($result['secondaryCluster']['name'] ?? null);
            $partnerSheet->getCell($column++ . $row)->setValue($result['officialStartDate'] ?? null);
            $partnerSheet->getCell($column++ . $row)->setValue($result['officialEndDate'] ?? null);
            $partnerSheet->getCell($column++ . $row)->setValue($result['duration']['months'] ?? null);
            $partnerSheet->getCell($column++ . $row)->setValue($result['status']['status'] ?? null);
            $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionTotalCosts']);
            $partnerSheet->getCell($column . $row)->setValue($result['latestVersionTotalEffort']);
        }

        $excelWriter = IOFactory::createWriter($spreadSheet, 'Xlsx');

        ob_start();
        $excelWriter->save('php://output');
        $file = ob_get_clean();

        $extension = '.xlsx';
        $mimetype  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return ['download' => base64_encode($file), 'extension' => $extension, 'mimetype' => $mimetype];
    }
}
