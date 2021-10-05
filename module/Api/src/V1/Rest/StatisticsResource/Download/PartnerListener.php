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
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class ResultsListener
 * @package Api\V1\Rest\StatisticsResource
 */
final class PartnerListener extends AbstractResourceListener
{
    private PartnerService      $partnerService;
    private UserService         $userService;
    private TranslatorInterface $translator;

    public function __construct(
        PartnerService $partnerService,
        UserService $userService,
        TranslatorInterface $translator
    ) {
        $this->partnerService = $partnerService;
        $this->userService    = $userService;
        $this->translator     = $translator;
    }

    public function fetch($id = null)
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }
        $output        = (int)$id;
        $encodedFilter = $this->getEvent()->getRouteMatch()->getParam('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        $results = $this->partnerService->getPartners($user->getFunder(), $arrayFilter);

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

        $excelWriter = IOFactory::createWriter($spreadSheet, 'Xlsx');

        ob_start();
        $excelWriter->save('php://output');
        $file = ob_get_clean();

        $extension = '.xlsx';
        $mimetype  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return ['download' => base64_encode($file), 'extension' => $extension, 'mimetype' => $mimetype];
    }
}
