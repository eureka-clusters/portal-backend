<?php

namespace DoctrineORMModule\Proxy\__CG__\Api\Entity\OAuth;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class AuthorizationCode extends \Api\Entity\OAuth\AuthorizationCode implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'id', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'authorizationCode', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'clientId', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'user', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'expires', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'redirectUri', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'scope', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'idToken'];
        }

        return ['__isInitialized__', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'id', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'authorizationCode', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'clientId', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'user', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'expires', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'redirectUri', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'scope', '' . "\0" . 'Api\\Entity\\OAuth\\AuthorizationCode' . "\0" . 'idToken'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (AuthorizationCode $proxy) {
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
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @deprecated no longer in use - generated code now relies on internal components rather than generated public API
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId(): int
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
    public function setId(int $id): \Api\Entity\OAuth\AuthorizationCode
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationCode(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAuthorizationCode', []);

        return parent::getAuthorizationCode();
    }

    /**
     * {@inheritDoc}
     */
    public function setAuthorizationCode(string $authorizationCode): \Api\Entity\OAuth\AuthorizationCode
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAuthorizationCode', [$authorizationCode]);

        return parent::setAuthorizationCode($authorizationCode);
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
    public function setClientId(string $clientId): \Api\Entity\OAuth\AuthorizationCode
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClientId', [$clientId]);

        return parent::setClientId($clientId);
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(): ?\Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUser', []);

        return parent::getUser();
    }

    /**
     * {@inheritDoc}
     */
    public function setUser(?\Admin\Entity\User $user): \Api\Entity\OAuth\AuthorizationCode
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUser', [$user]);

        return parent::setUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function getExpires(): \DateTimeImmutable
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getExpires', []);

        return parent::getExpires();
    }

    /**
     * {@inheritDoc}
     */
    public function setExpires(\DateTimeImmutable $expires): \Api\Entity\OAuth\AuthorizationCode
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setExpires', [$expires]);

        return parent::setExpires($expires);
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
    public function setRedirectUri(string $redirectUri): \Api\Entity\OAuth\AuthorizationCode
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRedirectUri', [$redirectUri]);

        return parent::setRedirectUri($redirectUri);
    }

    /**
     * {@inheritDoc}
     */
    public function getScope(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getScope', []);

        return parent::getScope();
    }

    /**
     * {@inheritDoc}
     */
    public function setScope(string $scope): \Api\Entity\OAuth\AuthorizationCode
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setScope', [$scope]);

        return parent::setScope($scope);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdToken(): ?string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdToken', []);

        return parent::getIdToken();
    }

    /**
     * {@inheritDoc}
     */
    public function setIdToken(?string $idToken): \Api\Entity\OAuth\AuthorizationCode
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIdToken', [$idToken]);

        return parent::setIdToken($idToken);
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
