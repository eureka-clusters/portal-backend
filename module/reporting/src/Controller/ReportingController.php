<?php

declare(strict_types=1);

namespace Reporting\Controller;

use Admin\Entity\User;
use Admin\Entity\User\Preferences;
use AzureOSS\Storage\Blob\Models\ListBlobsOptions;
use Doctrine\Common\Collections\ArrayCollection;
use Jield\Search\Controller\Plugin\GetFilter;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use Reporting\Service\StorageLocationService;

/**
 * @method FlashMessenger flashMessenger()
 * @method User identity()
 * @method GetFilter getFilter()
 */
final class ReportingController extends AbstractActionController
{

    public function __construct(
        private readonly StorageLocationService $storageLocationService,
    ) {
    }

    #[\Override]
    public function indexAction(): Response|ViewModel
    {
        error_reporting(error_level: E_ALL ^ E_DEPRECATED);

        $storageLocation = $this->storageLocationService->getDefaultStorageLocation();
        $reports         = new ArrayCollection();

        $blobClient = $this->storageLocationService->getBlobService();

        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setPrefix($storageLocation->getExcelFolder() . '/');

        $blobList = $blobClient->listBlobs(
            container: $storageLocation->getContainer(),
            options: $listBlobsOptions
        );

        foreach ($blobList->getBlobs() as $blob) {
            $reports->add($blob);
        }

        return new ViewModel(
            variables: [
                'reports'         => $reports,
                'storageLocation' => $storageLocation,
            ]
        );
    }
}
