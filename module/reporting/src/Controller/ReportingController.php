<?php

declare(strict_types=1);

namespace Reporting\Controller;

use Admin\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Jield\Search\Controller\Plugin\GetFilter;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
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

    public function indexAction(): Response|ViewModel
    {
        error_reporting(error_level: E_ALL ^ E_DEPRECATED);

        $storageLocation = $this->storageLocationService->getDefaultStorageLocation();
        $reports         = new ArrayCollection();

        $blobClient = $this->storageLocationService->getBlobService();

        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setPrefix($storageLocation->getExcelFolder() . '/');

        // Setting max result to 1 is just to demonstrate the continuation token.
        // It is not the recommended value in a product environment.
        //$listBlobsOptions->setMaxResults(1);

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
