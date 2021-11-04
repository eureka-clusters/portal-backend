<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Cluster\Service\ProjectService;
use Cluster\Provider\ProjectProvider;
use Cluster\Rest\Collection\ProjectCollection;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class ResultsListener
 * @package Api\V1\Rest\StatisticsResource
 */
final class ProjectListener extends AbstractResourceListener
{
    private ProjectService      $projectService;
    private UserService         $userService;
    private TranslatorInterface $translator;
    private ProjectProvider $projectProvider;

    public function __construct(
        ProjectService $projectService,
        UserService $userService,
        TranslatorInterface $translator,
        ProjectProvider $projectProvider
    ) {
        $this->projectService = $projectService;
        $this->userService    = $userService;
        $this->translator     = $translator;
        $this->projectProvider = $projectProvider;
    }

    public function fetch($filter = null)
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($filter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        // @johan what type is params? i included a zero offset with return of 100 entries for the time to fix this
        // $params->offset and $params->amount are not existing.
        // are they are coming from fetchAll($params = []), so what do we have to use to return all?
        // does offset = null, limit = null return anything or do i have to use -1 or something else?
        // i guess the pageSize is the params amount set in the module.config.php ?
        // 'page_size'                  => 25,
        // is this even available for 'type'    => Segment::class, ?

        $projects = $this->projectService->getProjects($user->getFunder(), $arrayFilter);
        $results = (new ProjectCollection($projects, $this->projectProvider))->getItems(
            null,
            null
            // 0,
            // 100
            // $params->offset,
            // $params->amount ?? 100
        );

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
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-latest-version'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-total-costs'));
        $partnerSheet->setCellValue($column . $row, $this->translator->translate('txt-total-effort'));


        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $partnerSheet->getCell($column++ . $row)->setValue($result['number']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['name']);
            $partnerSheet->getCell($column++ . $row)->setValue(isset($result['primaryCluster']['name'])?$result['primaryCluster']['name']:null);
            $partnerSheet->getCell($column++ . $row)->setValue(isset($result['secondaryCluster']['name'])?$result['secondaryCluster']['name']:null);
            $partnerSheet->getCell($column++ . $row)->setValue(isset($result['latestVersion']['type']['type'])?$result['latestVersion']['type']['type']: null);
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
