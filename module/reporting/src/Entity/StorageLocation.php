<?php

declare(strict_types=1);

namespace Reporting\Entity;

use Api\Repository\OAuth\Service;
use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModule\Form\Element\EntitySelect;
use Jield\Export\Entity\StorageLocationInterface;
use Jield\Export\Enum\ExportFileTypeEnum;
use Jield\Export\Enum\TypeEnum;
use Laminas\Form\Annotation\Attributes;
use Laminas\Form\Annotation\Options;
use Laminas\Form\Annotation\Type;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Text;
use Override;
use Reporting\Repository\StorageLocationRepository;

#[ORM\Table(name: 'reporting_storage_location')]
#[ORM\Entity(repositoryClass: StorageLocationRepository::class)]
class StorageLocation extends AbstractEntity implements StorageLocationInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Type(type: Hidden::class)]
    private ?int $id = null;

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: ['help-block' => 'txt-reporting-storage-location-name-help-block'])]
    #[Attributes(attributes: [
        'label'       => 'txt-reporting-storage-location-name-label',
        'placeholder' => 'txt-reporting-storage-location-name-placeholder'
    ])]
    private string $name = '';

    #[ORM\Column(type: 'text')]
    #[Type(type: Text::class)]
    #[Options(options: ['help-block' => 'txt-reporting-storage-location-connection-string-help-block'])]
    #[Attributes(attributes: [
        'label'       => 'txt-reporting-storage-location-connection-string-label',
        'placeholder' => 'txt-reporting-storage-location-connection-string-placeholder'
    ])]
    private string $connectionString = '';

    #[ORM\Column(enumType: ExportFileTypeEnum::class)]
    #[Type(type: Radio::class)]
    #[Options(options: [
        'help-block' => 'txt-reporting-storage-location-export-file-type-help-block'
    ])]
    #[Attributes(attributes: [
        'enum'  => ExportFileTypeEnum::class,
        'label' => 'txt-reporting-storage-location-export-file-type-label',
    ])]
    private ExportFileTypeEnum $exportFileType = ExportFileTypeEnum::PARQUET;

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: ['help-block' => 'txt-reporting-storage-location-container-help-block'])]
    #[Attributes(attributes: [
        'label'       => 'txt-reporting-storage-location-container-label',
        'placeholder' => 'txt-reporting-storage-location-container-placeholder'
    ])]
    private string $container = '';

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: ['help-block' => 'txt-reporting-storage-location-folder-help-block'])]
    #[Attributes(attributes: [
        'label'       => 'txt-reporting-storage-location-folder-label',
        'placeholder' => 'txt-reporting-storage-location-folder-placeholder'
    ])]
    private string $folder = '';

    #[ORM\ManyToOne(targetEntity: \Api\Entity\OAuth\Service::class, cascade: ['persist'], inversedBy: 'storageLocations')]
    #[ORM\JoinColumn(nullable: true)]
    #[Type(EntitySelect::class)]
    #[Options([
        'target_class' => \Api\Entity\OAuth\Service::class,
        'empty_option' => '— Select an oAuth2 service',
        'help-block'   => 'txt-reporting-storage-location-oauth2-service-help-block',
        'find_method'  => [
            'name'   => 'findBy',
            'params' => [
                'criteria' => [],
                'orderBy'  => ['scope' => Order::Ascending->value]
            ],
        ],
    ])]
    #[Attributes(attributes: [
        'label' => 'txt-reporting-storage-location-oauth2-service-label',
    ])]
    private ?\Api\Entity\OAuth\Service $oAuth2Service = null;

    public function __construct()
    {
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public function hasOAuth2Service(): bool
    {
        return $this->oAuth2Service instanceof \Api\Entity\OAuth\Service;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Override]
    public function setId(int $id): StorageLocation
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): StorageLocation
    {
        $this->name = $name;
        return $this;
    }

    public function getConnectionString(): string
    {
        return $this->connectionString;
    }

    public function setConnectionString(string $connectionString): StorageLocation
    {
        $this->connectionString = $connectionString;
        return $this;
    }

    public function getExportFileType(): ExportFileTypeEnum
    {
        return $this->exportFileType;
    }

    public function setExportFileType(ExportFileTypeEnum $exportFileType): StorageLocation
    {
        $this->exportFileType = $exportFileType;
        return $this;
    }

    public function getContainer(): string
    {
        return $this->container;
    }

    public function setContainer(string $container): StorageLocation
    {
        $this->container = $container;
        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): StorageLocation
    {
        $this->folder = $folder;
        return $this;
    }

    public function getOAuth2Service(): ?\Api\Entity\OAuth\Service
    {
        return $this->oAuth2Service;
    }

    public function setOAuth2Service(?\Api\Entity\OAuth\Service $oAuth2Service): StorageLocation
    {
        $this->oAuth2Service = $oAuth2Service;
        return $this;
    }

    public function getType(): TypeEnum
    {
        return TypeEnum::EXPORT;
    }

}
