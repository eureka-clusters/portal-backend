<?php

declare(strict_types=1);

namespace Reporting\Service;

use Admin\Service\OAuth2Service;
use Application\Service\AbstractService;
use Doctrine\ORM\EntityManager;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Reporting\Entity\StorageLocation;

class StorageLocationService extends AbstractService implements \Jield\Export\Service\StorageLocationServiceInterface
{
    private ?BlobRestProxy $blobClient = null;

    public function __construct(EntityManager $entityManager, private readonly OAuth2Service $oAuth2Service)
    {
        parent::__construct(entityManager: $entityManager);
    }

    public function findStorageLocationById(int $id): ?\Reporting\Entity\StorageLocation
    {
        return $this->entityManager->getRepository(entityName: StorageLocation::class)->find(id: $id);
    }

    public function getBlobService(): \MicrosoftAzure\Storage\Blob\BlobRestProxy
    {
        if (null !== $this->blobClient) {
            return $this->blobClient;
        }

        $storageLocation = $this->getDefaultStorageLocation();

        if ($storageLocation->hasOAuth2Service()) {
            $accessToken = $this->oAuth2Service->fetchAccessTokenFromService($storageLocation->getOAuth2Service());

            $this->blobClient = BlobRestProxy::createBlobServiceWithTokenCredential(
                token: $accessToken,
                connectionString: $storageLocation->getConnectionString()
            );
        } else {
            $this->blobClient = BlobRestProxy::createBlobService(
                connectionString: $storageLocation->getConnectionString()
            );
        }

        return $this->blobClient;
    }

    public function getDefaultStorageLocation(): \Reporting\Entity\StorageLocation
    {
        return $this->entityManager->getRepository(entityName: StorageLocation::class)->findOneBy(criteria: []);
    }

    public function canDeleteStorageLocation(StorageLocation $storageLocation): bool
    {
        return true;
    }
}
