<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Api\Enum\OAuth2\ServiceTypeEnum;
use Application\Entity\AbstractEntity;
use Cluster\Entity\Cluster;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntitySelect;
use JetBrains\PhpStorm\Pure;
use Laminas\Form\Annotation;
use Laminas\Form\Annotation\Exclude;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Element\Url;
use Reporting\Entity\StorageLocation;

#[ORM\Table(name: 'oauth_service')]
#[ORM\Entity(repositoryClass: \Api\Repository\OAuth\ServiceRepository::class)]
class Service extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Annotation\Type(Hidden::class)]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Annotation\Type(type: Text::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-name-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-name-label',
        'placeholder' => 'txt-oauth2-service-name-placeholder',
    ])]
    private string $name = '';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Annotation\Type(type: Textarea::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-description-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-description-label',
        'placeholder' => 'txt-oauth2-service-description-placeholder',
    ])]
    private ?string $description = null;

    #[ORM\Column]
    #[Annotation\Type(type: Text::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-client-id-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-client-id-label',
        'placeholder' => 'txt-oauth2-service-client-id-placeholder',
    ])]
    private string $clientId = '';

    #[ORM\Column(length: 2000)]
    #[Annotation\Type(type: Text::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-client-secret-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-client-secret-label',
        'placeholder' => 'txt-oauth2-service-client-secret-placeholder',
    ])]
    private string $clientSecret = '';

    #[ORM\Column]
    #[Annotation\Type(type: Url::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-redirect-url-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-redirect-url-label',
        'placeholder' => 'txt-oauth2-service-redirect-url-placeholder',
    ])]
    private string $redirectUrl = '';

    #[ORM\Column(nullable: true)]
    #[Annotation\Type(type: Text::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-tenant-id-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-tenant-id-label',
        'placeholder' => 'txt-oauth2-service-tenant-id-placeholder',
    ])]
    private ?string $tenantId = null;

    #[ORM\Column]
    #[Annotation\Type(type: Url::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-authorization-url-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-authorization-url-label',
        'placeholder' => 'txt-oauth2-service-authorization-url-placeholder',
    ])]
    private string $authorizationUrl = '';

    #[ORM\Column]
    #[Annotation\Type(type: Url::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-access-token-url-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-access-token-url-label',
        'placeholder' => 'txt-oauth2-service-access-token-url-placeholder',
    ])]
    private string $accessTokenUrl = '';

    #[ORM\Column]
    #[Annotation\Type(type: Url::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-profile-url-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-profile-url-label',
        'placeholder' => 'txt-oauth2-service-profile-url-placeholder',
    ])]
    private string $profileUrl = '';

    #[ORM\ManyToMany(targetEntity: Cluster::class, inversedBy: 'oauthServices', cascade: ['persist'])]
    #[ORM\OrderBy(value: ['name' => \Doctrine\Common\Collections\Order::Ascending->value])]
    #[ORM\JoinTable(name: 'oauth_service_cluster')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\InverseJoinColumn(nullable: false)]
    #[Annotation\Type(EntityMultiCheckbox::class)]
    #[Annotation\Options([
        'help-block'   => 'txt-oauth2-service-allowed-clusters-help-block',
        'target_class' => Cluster::class,
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['name' => \Doctrine\Common\Collections\Order::Ascending->value]],
        ],
    ])]
    #[Annotation\Attributes(['label' => 'txt-oauth2-service-allowed-clusters-label'])]
    private Collection $allowedClusters;

    #[ORM\ManyToOne(targetEntity: Scope::class, cascade: ['persist'], inversedBy: 'oAuthServices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Annotation\Type(EntitySelect::class)]
    #[Annotation\Options([
        'target_class' => Scope::class,
        'empty_option' => '— Select a scope',
        'label'        => 'txt-oauth2-service-scope-label',
        'help-block'   => 'txt-oauth2-service-scope-help-block',
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['scope' => \Doctrine\Common\Collections\Order::Ascending->value]],
        ],
    ])]
    private Scope $scope;

    #[ORM\Column(type: 'smallint', nullable: false, enumType: ServiceTypeEnum::class)]
    #[Exclude]
    private ServiceTypeEnum $type = ServiceTypeEnum::OTHER;

    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'], inversedBy: 'oAuthServices')]
    #[ORM\JoinColumn(referencedColumnName: 'client_id', nullable: true)]
    #[Annotation\Type(EntitySelect::class)]
    #[Annotation\Options([
        'target_class' => Client::class,
        'empty_option' => '— Select a client',
        'label'        => 'txt-oauth2-service-client-label',
        'help-block'   => 'txt-oauth2-service-client-help-block',
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['name' => \Doctrine\Common\Collections\Order::Ascending->value]],
        ],
    ])]
    private ?Client $client = null;

    #[ORM\Column(length: 2000, nullable: true)]
    #[Annotation\Type(type: Textarea::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth2-service-private-key-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth2-service-private-key-label',
        'placeholder' => 'txt-oauth2-service-private-key-placeholder',
    ])]
    private ?string $privateKey = null;

    #[ORM\OneToMany(mappedBy: 'oAuth2Service', targetEntity: StorageLocation::class, cascade: ['persist'])]
    private Collection $storageLocations;

    #[\Override]
    public function __toString(): string
    {
        return $this->name;
    }

    #[Pure]
    public function __construct()
    {
        $this->allowedClusters  = new ArrayCollection();
        $this->storageLocations = new ArrayCollection();
        $this->scope            = new Scope();
    }

    public function addAllowedClusters(Collection $allowedClustersCollection): void
    {
        foreach ($allowedClustersCollection as $allowedClusters) {
            $this->allowedClusters->add($allowedClusters);
        }
    }

    public function removeAllowedClusters(Collection $allowedClustersCollection): void
    {
        foreach ($allowedClustersCollection as $single) {
            $this->allowedClusters->removeElement($single);
        }
    }

    public function parseOptions(): array
    {
        return [
            'urlAuthorize'            => $this->authorizationUrl,
            'urlAccessToken'          => $this->accessTokenUrl,
            'clientId'                => $this->clientId,
            'clientSecret'            => $this->clientSecret,
            'redirectUri'             => $this->redirectUrl,
            'urlResourceOwnerDetails' => $this->allowedClusters->first()->getName(),
        ];
    }

    #[\Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Service
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Service
    {
        $this->name = $name;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): Service
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): Service
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(string $redirectUrl): Service
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    public function getAuthorizationUrl(): string
    {
        return $this->authorizationUrl;
    }

    public function setAuthorizationUrl(string $authorizationUrl): Service
    {
        $this->authorizationUrl = $authorizationUrl;
        return $this;
    }

    public function getAccessTokenUrl(): string
    {
        if ($this->getType()->isMicrosoft()) {
            return 'https://login.microsoftonline.com/' . $this->getTenantId() . '/oauth2/v2.0/token';
        }

        return $this->accessTokenUrl;
    }

    public function setAccessTokenUrl(string $accessTokenUrl): Service
    {
        $this->accessTokenUrl = $accessTokenUrl;
        return $this;
    }

    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    public function setProfileUrl(string $profileUrl): Service
    {
        $this->profileUrl = $profileUrl;
        return $this;
    }

    public function getAllowedClusters(): Collection
    {
        return $this->allowedClusters;
    }

    public function setAllowedClusters(Collection $allowedClusters): Service
    {
        $this->allowedClusters = $allowedClusters;
        return $this;
    }

    public function getScope(): Scope
    {
        return $this->scope;
    }

    public function setScope(Scope $scope): Service
    {
        $this->scope = $scope;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): Service
    {
        $this->client = $client;
        return $this;
    }

    public function getStorageLocations(): Collection
    {
        return $this->storageLocations;
    }

    public function setStorageLocations(Collection $storageLocations): Service
    {
        $this->storageLocations = $storageLocations;
        return $this;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function setTenantId(?string $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function getType(): ServiceTypeEnum|int
    {
        return $this->type;
    }

    public function setType(ServiceTypeEnum|int $type): void
    {
        $this->type = $type;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(?string $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
