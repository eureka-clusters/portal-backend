<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Cluster;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntitySelect;
use JetBrains\PhpStorm\Pure;
use Laminas\Form\Annotation;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Url;
use Reporting\Entity\StorageLocation;

#[ORM\Table(name: 'oauth_service')]
#[ORM\Entity(repositoryClass: \Api\Repository\OAuth\Service::class)]
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
        'help-block' => 'txt-oauth-service-name-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth-service-name-label',
        'placeholder' => 'txt-oauth-service-name-placeholder',
    ])]
    private string $name = '';

    #[ORM\Column]
    #[Annotation\Type(type: Text::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth-service-client-id-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth-service-client-id-label',
        'placeholder' => 'txt-oauth-service-client-id-placeholder',
    ])]
    private string $clientId = '';

    #[ORM\Column(length: 2000)]
    #[Annotation\Type(type: Text::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth-service-client-secret-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth-service-client-secret-label',
        'placeholder' => 'txt-oauth-service-client-secret-placeholder',
    ])]
    private string $clientSecret = '';

    #[ORM\Column]
    #[Annotation\Type(type: Url::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth-service-redirect-url-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth-service-redirect-url-label',
        'placeholder' => 'txt-oauth-service-redirect-url-placeholder',
    ])]
    private string $redirectUrl = '';

    #[ORM\Column]
    #[Annotation\Type(type: Url::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth-service-authorization-url-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth-service-authorization-url-label',
        'placeholder' => 'txt-oauth-service-authorization-url-placeholder',
    ])]
    private string $authorizationUrl = '';

    #[ORM\Column]
    #[Annotation\Type(type: Url::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth-service-access-token-url-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth-service-access-token-url-label',
        'placeholder' => 'txt-oauth-service-access-token-url-placeholder',
    ])]
    private string $accessTokenUrl = '';

    #[ORM\Column]
    #[Annotation\Type(type: Url::class)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-oauth-service-profile-url-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-oauth-service-profile-url-label',
        'placeholder' => 'txt-oauth-service-profile-url-placeholder',
    ])]
    private string $profileUrl = '';

    #[ORM\ManyToMany(targetEntity: Cluster::class, inversedBy: 'oauthServices', cascade: ['persist'])]
    #[ORM\OrderBy(value: ['name' => Criteria::ASC])]
    #[ORM\JoinTable(name: 'oauth_service_cluster')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\InverseJoinColumn(nullable: false)]
    #[Annotation\Type(EntityMultiCheckbox::class)]
    #[Annotation\Options([
        'help-block'   => 'txt-oauth-service-allowed-clusters-help-block',
        'target_class' => Cluster::class,
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['name' => Criteria::ASC]],
        ],
    ])]
    #[Annotation\Attributes(['label' => 'txt-oauth-service-allowed-clusters-label'])]
    private Collection $allowedClusters;

    #[ORM\ManyToOne(targetEntity: Scope::class, cascade: ['persist'], inversedBy: 'oAuthServices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Annotation\Type(EntitySelect::class)]
    #[Annotation\Options([
        'target_class' => Scope::class,
        'empty_option' => '— Select a scope',
        'label'        => 'txt-oauth-service-scope-label',
        'help-block'   => 'txt-oauth-service-scope-help-block',
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['scope' => Criteria::ASC]],
        ],
    ])]
    private Scope $scope;

    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'], inversedBy: 'oAuthServices')]
    #[ORM\JoinColumn(referencedColumnName: 'client_id', nullable: false)]
    #[Annotation\Type(EntitySelect::class)]
    #[Annotation\Options([
        'target_class' => Client::class,
        'empty_option' => '— Select a client',
        'label'        => 'txt-oauth-service-client-label',
        'help-block'   => 'txt-oauth-service-client-help-block',
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['name' => Criteria::ASC]],
        ],
    ])]
    private Client $client;

    #[ORM\OneToMany(mappedBy: 'oAuth2Service', targetEntity: StorageLocation::class, cascade: ['persist'])]
    private Collection $storageLocations;

    #[Pure] public function __construct()
    {
        $this->allowedClusters  = new ArrayCollection();
        $this->storageLocations = new ArrayCollection();
        $this->scope            = new Scope();
        $this->client           = new Client();
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

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): Service
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
}
