<?php

declare(strict_types=1);

namespace Reporting\Service;

use Admin\Service\oAuth2Service;
use Application\Service\AbstractService;
use AzureOSS\Storage\Blob\BlobRestProxy;
use Doctrine\ORM\EntityManager;
use Jield\Export\Service\StorageLocationServiceInterface;
use Override;
use Reporting\Entity\StorageLocation;

class StorageLocationService extends AbstractService implements StorageLocationServiceInterface
{
    private ?BlobRestProxy $blobClient = null;

    public function __construct(EntityManager $entityManager, private readonly oAuth2Service $oAuth2Service)
    {
        parent::__construct(entityManager: $entityManager);
    }

    public function findStorageLocationById(int $id): ?StorageLocation
    {
        return $this->entityManager->getRepository(entityName: StorageLocation::class)->find(id: $id);
    }

    #[Override]
    public function getBlobService(): BlobRestProxy
    {
        if ($this->blobClient instanceof BlobRestProxy) {
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

    #[Override]
    public function getDefaultStorageLocation(): StorageLocation
    {
        return $this->entityManager->getRepository(entityName: StorageLocation::class)->findOneBy(criteria: []);
    }

    public function hasDefaultStorageLocation(): bool
    {
        return $this->entityManager->getRepository(entityName: StorageLocation::class)->count(criteria: []) !== 0;
    }

    public function canDeleteStorageLocation(StorageLocation $storageLocation): bool
    {
        return true;
    }
}
