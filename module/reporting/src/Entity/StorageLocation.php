<?php

declare(strict_types=1);

namespace Reporting\Entity;

use Api\Entity\OAuth\Service;
use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModule\Form\Element\EntitySelect;
use JetBrains\PhpStorm\Pure;
use Laminas\Form\Annotation\Attributes;
use Laminas\Form\Annotation\Options;
use Laminas\Form\Annotation\Type;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;

#[ORM\Table(name: 'reporting_storage_location')]
#[ORM\Entity(repositoryClass: \Reporting\Repository\StorageLocation::class)]
class StorageLocation extends AbstractEntity implements \Jield\Export\Entity\StorageLocationInterface
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

    #[ORM\Column(length: 2000)]
    #[Type(type: Text::class)]
    #[Options(options: ['help-block' => 'txt-reporting-storage-location-connection-string-help-block'])]
    #[Attributes(attributes: [
        'label'       => 'txt-reporting-storage-location-connection-string-label',
        'placeholder' => 'txt-reporting-storage-location-connection-string-placeholder'
    ])]
    private string $connectionString = '';

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
    #[Options(options: ['help-block' => 'txt-reporting-storage-location-excel-folder-help-block'])]
    #[Attributes(attributes: [
        'label'       => 'txt-reporting-storage-location-excel-folder-label',
        'placeholder' => 'txt-reporting-storage-location-excel-folder-placeholder'
    ])]
    private string $excelFolder = '';

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: ['help-block' => 'txt-reporting-storage-location-parquet-folder-help-block'])]
    #[Attributes(attributes: [
        'label'       => 'txt-reporting-storage-location-parquet-folder-label',
        'placeholder' => 'txt-reporting-storage-location-parquet-folder-placeholder'
    ])]
    private string $parquetFolder = '';

    #[ORM\ManyToOne(targetEntity: Service::class, cascade: ['persist'], inversedBy: 'storageLocations')]
    #[ORM\JoinColumn(nullable: true)]
    #[Type(EntitySelect::class)]
    #[Options([
        'target_class' => Service::class,
        'empty_option' => '— Select an oAuth2 service',
        'help-block'   => 'txt-reporting-storage-location-oauth2-service-help-block',
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['scope' => Criteria::ASC]],
        ],
    ])]
    #[Attributes(attributes: [
        'label' => 'txt-reporting-storage-location-oauth2-service-label',
    ])]
    private ?Service $oAuth2Service = null;

    #[Pure] public function __construct()
    {
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function hasOAuth2Service(): bool
    {
        return $this->oAuth2Service !== null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): StorageLocation
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

    public function getExcelFolder(): string
    {
        return $this->excelFolder;
    }

    public function setExcelFolder(string $excelFolder): StorageLocation
    {
        $this->excelFolder = $excelFolder;
        return $this;
    }

    public function getParquetFolder(): string
    {
        return $this->parquetFolder;
    }

    public function setParquetFolder(string $parquetFolder): StorageLocation
    {
        $this->parquetFolder = $parquetFolder;
        return $this;
    }

    public function getOAuth2Service(): ?Service
    {
        return $this->oAuth2Service;
    }

    public function setOAuth2Service(?Service $oAuth2Service): StorageLocation
    {
        $this->oAuth2Service = $oAuth2Service;
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
}
