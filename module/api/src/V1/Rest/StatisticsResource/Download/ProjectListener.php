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
        private readonly ProjectService $projectService,
        private readonly UserService $userService,
        private readonly TranslatorInterface $translator,
        private readonly ProjectProvider $projectProvider
    ) {
    }

    public function fetch($id = 'Xlsx'): array
    {
        $user = $this->userService->findUserById(id: (int) $this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user) {
            return [];
        }

        //The filter is a base64 encoded serialised json string
        $filter      = $this->getEvent()->getQueryParams()?->get(name: 'filter');
        $filter      = base64_decode(string: $filter, strict: true);
        $arrayFilter = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);

        $defaultSort = 'project.name';
        $sort        = $this->getEvent()->getQueryParams()?->get(name: 'sort', default: $defaultSort);
        $order       = $this->getEvent()->getQueryParams()?->get(name: 'order', default: 'asc');

        $projectQueryBuilder = $this->projectService->getProjects(
            user: $user,
            filter: $arrayFilter,
            sort: $sort,
            order: $order
        );

        $projects = $projectQueryBuilder->getQuery()->getResult();

        $results = [];
        /** @var Project $project */
        foreach ($projects as $project) {
            $results[] = $this->projectProvider->generateArray(project: $project);
        }

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getProperties()->setTitle(title: 'Statistics');
        $partnerSheet = $spreadSheet->getActiveSheet();

        $partnerSheet->setTitle(title: $this->translator->translate(message: 'txt-projects'));

        $row    = 1;
        $column = 'A';
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-number'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-name'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-primary-cluster'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-secondary-cluster'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-official-start-date'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-official-end-date'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-duration-(months)'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-status'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-total-costs'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column . $row,
            value: $this->translator->translate(
                message: 'txt-total-effort'
            )
        );

        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['number']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['name']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['primaryCluster']['name'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['secondaryCluster']['name'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['officialStartDate'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['officialEndDate'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['duration']['months'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['status']['status'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionTotalCosts']);
            $partnerSheet->getCell(coordinate: $column . $row)->setValue(value: $result['latestVersionTotalEffort']);
        }

        $excelWriter = IOFactory::createWriter(spreadsheet: $spreadSheet, writerType: 'Xlsx');

        ob_start();
        $excelWriter->save(filename: 'php://output');
        $file = ob_get_clean();

        $extension = '.xlsx';
        $mimetype  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return ['download' => base64_encode(string: $file), 'extension' => $extension, 'mimetype' => $mimetype];
    }
}
