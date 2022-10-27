<?php

namespace DoctrineORMModule\Proxy\__CG__\Api\Entity\OAuth;


/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Service extends \Api\Entity\OAuth\Service implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Proxy\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Proxy\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array<string, null> properties to be lazy loaded, indexed by property name
     */
    public static $lazyPropertiesNames = array (
);

    /**
     * @var array<string, mixed> default values of properties to be lazy loaded, with keys being the property names
     *
     * @see \Doctrine\Common\Proxy\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array (
);



    public function __construct(?\Closure $initializer = null, ?\Closure $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'id', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'name', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'clientId', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'clientSecret', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'redirectUrl', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'authorizationUrl', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'accessTokenUrl', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'profileUrl', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'allowedClusters', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'scope', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'client'];
        }

        return ['__isInitialized__', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'id', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'name', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'clientId', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'clientSecret', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'redirectUrl', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'authorizationUrl', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'accessTokenUrl', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'profileUrl', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'allowedClusters', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'scope', '' . "\0" . 'Api\\Entity\\OAuth\\Service' . "\0" . 'client'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Service $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy::$lazyPropertiesDefaults as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load(): void
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized(): bool
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized): void
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null): void
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer(): ?\Closure
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null): void
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner(): ?\Closure
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @deprecated no longer in use - generated code now relies on internal components rather than generated public API
     * @static
     */
    public function __getLazyProperties(): array
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function addAllowedClusters(\Doctrine\Common\Collections\Collection $allowedClustersCollection): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addAllowedClusters', [$allowedClustersCollection]);

        parent::addAllowedClusters($allowedClustersCollection);
    }

    /**
     * {@inheritDoc}
     */
    public function removeAllowedClusters(\Doctrine\Common\Collections\Collection $allowedClustersCollection): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeAllowedClusters', [$allowedClustersCollection]);

        parent::removeAllowedClusters($allowedClustersCollection);
    }

    /**
     * {@inheritDoc}
     */
    public function parseOptions(): array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'parseOptions', []);

        return parent::parseOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): ?int
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId(?int $id): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', []);

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setName(string $name): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$name]);

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientId(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClientId', []);

        return parent::getClientId();
    }

    /**
     * {@inheritDoc}
     */
    public function setClientId(string $clientId): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClientId', [$clientId]);

        return parent::setClientId($clientId);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientSecret(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClientSecret', []);

        return parent::getClientSecret();
    }

    /**
     * {@inheritDoc}
     */
    public function setClientSecret(string $clientSecret): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClientSecret', [$clientSecret]);

        return parent::setClientSecret($clientSecret);
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectUrl(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRedirectUrl', []);

        return parent::getRedirectUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function setRedirectUrl(string $redirectUrl): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRedirectUrl', [$redirectUrl]);

        return parent::setRedirectUrl($redirectUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationUrl(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAuthorizationUrl', []);

        return parent::getAuthorizationUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function setAuthorizationUrl(string $authorizationUrl): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAuthorizationUrl', [$authorizationUrl]);

        return parent::setAuthorizationUrl($authorizationUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessTokenUrl(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAccessTokenUrl', []);

        return parent::getAccessTokenUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function setAccessTokenUrl(string $accessTokenUrl): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAccessTokenUrl', [$accessTokenUrl]);

        return parent::setAccessTokenUrl($accessTokenUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileUrl(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProfileUrl', []);

        return parent::getProfileUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function setProfileUrl(string $profileUrl): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProfileUrl', [$profileUrl]);

        return parent::setProfileUrl($profileUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedClusters(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAllowedClusters', []);

        return parent::getAllowedClusters();
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowedClusters(\Doctrine\Common\Collections\Collection $allowedClusters): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAllowedClusters', [$allowedClusters]);

        return parent::setAllowedClusters($allowedClusters);
    }

    /**
     * {@inheritDoc}
     */
    public function getScope(): \Api\Entity\OAuth\Scope
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getScope', []);

        return parent::getScope();
    }

    /**
     * {@inheritDoc}
     */
    public function setScope(\Api\Entity\OAuth\Scope $scope): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setScope', [$scope]);

        return parent::setScope($scope);
    }

    /**
     * {@inheritDoc}
     */
    public function getClient(): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClient', []);

        return parent::getClient();
    }

    /**
     * {@inheritDoc}
     */
    public function setClient(\Api\Entity\OAuth\Client $client): \Api\Entity\OAuth\Service
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClient', [$client]);

        return parent::setClient($client);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', []);

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceId(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getResourceId', []);

        return parent::getResourceId();
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $switch): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'get', [$switch]);

        return parent::get($switch);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isEmpty', []);

        return parent::isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $prop): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'has', [$prop]);

        return parent::has($prop);
    }

}
