<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Api\V1\Rest\StatisticsResource;

use Cluster\Entity\Statistics\Partner;
use Cluster\Service\StatisticsService;
use Contact\Service\ContactService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class ResultsListener
 * @package Api\V1\Rest\StatisticsResource
 */
final class DownloadListener extends AbstractResourceListener
{
    private StatisticsService $statisticsService;
    private ContactService $contactService;
    private TranslatorInterface $translator;

    public function __construct(StatisticsService $statisticsService, ContactService $contactService, TranslatorInterface $translator)
    {
        $this->statisticsService = $statisticsService;
        $this->contactService    = $contactService;
        $this->translator        = $translator;
    }

    public function fetch($id = null)
    {
        $contact = $this->contactService->findContactById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $contact || !$contact->isFunder()) {
            return [];
        }
        $output        = (int)$id;
        $encodedFilter = $this->getEvent()->getRouteMatch()->getParam('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        $results = $this->statisticsService->getResults($contact->getFunder(), $arrayFilter, $output);

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getProperties()->setTitle('Statistics');
        $partnerSheet = $spreadSheet->getActiveSheet();

        if ($output === Partner::RESULT_PROJECT) {

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


            /** @var Partner $result */
            foreach ($results as $result) {
                $column = 'A';
                $row++;

                $partnerSheet->getCell($column++ . $row)->setValue($result['projectNumber']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['projectName']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['primaryCluster']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['secondaryCluster']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionType']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionTotalCosts']);
                $partnerSheet->getCell($column . $row)->setValue($result['latestVersionTotalEffort']);
            }
        }

        if ($output === Partner::RESULT_PARTNER) {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getProperties()->setTitle('Statistics');
            $partnerSheet = $spreadSheet->getActiveSheet();
            $partnerSheet->setTitle($this->translator->translate('txt-partners'));

            $row    = 1;
            $column = 'A';
            $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-project-number'));
            $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-project-name'));
            $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-partner'));
            $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-country'));
            $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-partner-type'));
            $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-partner-costs'));
            $partnerSheet->setCellValue($column . $row, $this->translator->translate('txt-partner-effort'));


            /** @var Partner $result */
            foreach ($results as $result) {
                $column = 'A';
                $row++;

                $partnerSheet->getCell($column++ . $row)->setValue($result['projectNumber']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['projectName']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['partner']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['country']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['partnerType']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionType']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionCosts']);
                $partnerSheet->getCell($column . $row)->setValue($result['latestVersionEffort']);
            }
        }

        $excelWriter = IOFactory::createWriter($spreadSheet, 'Xlsx');

        ob_start();
        $excelWriter->save('php://output');
        $test = ob_get_clean();

        print $test;

        return [$test];
    }
}
