<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource;

use Admin\Service\UserService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use function base64_decode;
use function base64_encode;
use function json_decode;
use function ob_get_clean;
use function ob_start;

use const JSON_THROW_ON_ERROR;

/**
 * @deprecated
 */
final class DownloadListener extends AbstractResourceListener
{
    private UserService $userService;
    private TranslatorInterface $translator;

    public function __construct(
        UserService $userService,
        TranslatorInterface $translator
    ) {
        $this->userService = $userService;
        $this->translator  = $translator;
    }

    public function fetch($id = null)
    {
        $user = $this->userService->findUserById((int) $this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user || ! $user->isFunder()) {
            return [];
        }
        $output        = (int) $id;
        $encodedFilter = $this->getEvent()->getRouteMatch()->getParam('filter');

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        $results = [];

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getProperties()->setTitle('Statistics');
        $partnerSheet = $spreadSheet->getActiveSheet();

        if ($output === 1) {
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

                $partnerSheet->getCell($column++ . $row)->setValue($result['projectNumber']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['projectName']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['primaryCluster']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['secondaryCluster']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionType']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionTotalCosts']);
                $partnerSheet->getCell($column . $row)->setValue($result['latestVersionTotalEffort']);
            }
        }

        if ($output === 1) {
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
