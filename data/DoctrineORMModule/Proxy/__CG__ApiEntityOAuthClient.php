<?php

namespace DoctrineORMModule\Proxy\__CG__\Api\Entity\OAuth;


/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Client extends \Api\Entity\OAuth\Client implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'clientId', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'clientsecret', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'name', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'description', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'clientsecretTeaser', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'redirectUri', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'grantTypes', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'scope', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'jwtTokens', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'accessTokens', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'authorizationCodes', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'refreshTokens', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'publicKey', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'oAuthServices'];
        }

        return ['__isInitialized__', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'clientId', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'clientsecret', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'name', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'description', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'clientsecretTeaser', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'redirectUri', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'grantTypes', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'scope', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'jwtTokens', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'accessTokens', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'authorizationCodes', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'refreshTokens', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'publicKey', '' . "\0" . 'Api\\Entity\\OAuth\\Client' . "\0" . 'oAuthServices'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Client $proxy) {
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
    public function __toString(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', []);

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): ?string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId(string|int $clientId): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$clientId]);

        return parent::setId($clientId);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientId(): string
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getClientId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClientId', []);

        return parent::getClientId();
    }

    /**
     * {@inheritDoc}
     */
    public function setClientId(string $clientId): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClientId', [$clientId]);

        return parent::setClientId($clientId);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientsecret(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClientsecret', []);

        return parent::getClientsecret();
    }

    /**
     * {@inheritDoc}
     */
    public function setClientsecret(string $clientsecret): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClientsecret', [$clientsecret]);

        return parent::setClientsecret($clientsecret);
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
    public function setName(string $name): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$name]);

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDescription', []);

        return parent::getDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription(string $description): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDescription', [$description]);

        return parent::setDescription($description);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientsecretTeaser(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClientsecretTeaser', []);

        return parent::getClientsecretTeaser();
    }

    /**
     * {@inheritDoc}
     */
    public function setClientsecretTeaser(string $clientsecretTeaser): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClientsecretTeaser', [$clientsecretTeaser]);

        return parent::setClientsecretTeaser($clientsecretTeaser);
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectUri(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRedirectUri', []);

        return parent::getRedirectUri();
    }

    /**
     * {@inheritDoc}
     */
    public function setRedirectUri(string $redirectUri): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRedirectUri', [$redirectUri]);

        return parent::setRedirectUri($redirectUri);
    }

    /**
     * {@inheritDoc}
     */
    public function getGrantTypes(): ?string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGrantTypes', []);

        return parent::getGrantTypes();
    }

    /**
     * {@inheritDoc}
     */
    public function setGrantTypes(?string $grantTypes): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGrantTypes', [$grantTypes]);

        return parent::setGrantTypes($grantTypes);
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
    public function setScope(\Api\Entity\OAuth\Scope $scope): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setScope', [$scope]);

        return parent::setScope($scope);
    }

    /**
     * {@inheritDoc}
     */
    public function getJwtTokens(): \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getJwtTokens', []);

        return parent::getJwtTokens();
    }

    /**
     * {@inheritDoc}
     */
    public function setJwtTokens(\Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $jwtTokens): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setJwtTokens', [$jwtTokens]);

        return parent::setJwtTokens($jwtTokens);
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessTokens(): \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAccessTokens', []);

        return parent::getAccessTokens();
    }

    /**
     * {@inheritDoc}
     */
    public function setAccessTokens(\Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $accessTokens): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAccessTokens', [$accessTokens]);

        return parent::setAccessTokens($accessTokens);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationCodes(): \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAuthorizationCodes', []);

        return parent::getAuthorizationCodes();
    }

    /**
     * {@inheritDoc}
     */
    public function setAuthorizationCodes(\Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $authorizationCodes): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAuthorizationCodes', [$authorizationCodes]);

        return parent::setAuthorizationCodes($authorizationCodes);
    }

    /**
     * {@inheritDoc}
     */
    public function getRefreshTokens(): \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRefreshTokens', []);

        return parent::getRefreshTokens();
    }

    /**
     * {@inheritDoc}
     */
    public function setRefreshTokens(\Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $refreshTokens): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRefreshTokens', [$refreshTokens]);

        return parent::setRefreshTokens($refreshTokens);
    }

    /**
     * {@inheritDoc}
     */
    public function getPublicKey(): ?\Api\Entity\OAuth\PublicKey
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPublicKey', []);

        return parent::getPublicKey();
    }

    /**
     * {@inheritDoc}
     */
    public function setPublicKey(?\Api\Entity\OAuth\PublicKey $publicKey): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPublicKey', [$publicKey]);

        return parent::setPublicKey($publicKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getOAuthServices(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOAuthServices', []);

        return parent::getOAuthServices();
    }

    /**
     * {@inheritDoc}
     */
    public function setOAuthServices(\Doctrine\Common\Collections\Collection $oAuthServices): \Api\Entity\OAuth\Client
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOAuthServices', [$oAuthServices]);

        return parent::setOAuthServices($oAuthServices);
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
