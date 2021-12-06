<?php

namespace DoctrineORMModule\Proxy\__CG__\Admin\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class User extends \Admin\Entity\User implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'id', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'password', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'firstName', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'lastName', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'email', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'dateCreated', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'lastUpdate', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'dateEnd', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'roles', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'session', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'funder', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'oAuthAccessTokens', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'oAuthAuthorizationCodes', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'oAuthRefreshTokens', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'oAuthJwt'];
        }

        return ['__isInitialized__', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'id', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'password', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'firstName', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'lastName', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'email', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'dateCreated', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'lastUpdate', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'dateEnd', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'roles', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'session', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'funder', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'oAuthAccessTokens', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'oAuthAuthorizationCodes', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'oAuthRefreshTokens', '' . "\0" . 'Admin\\Entity\\User' . "\0" . 'oAuthJwt'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (User $proxy) {
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
    public function getRolesAsArray(): array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRolesAsArray', []);

        return parent::getRolesAsArray();
    }

    /**
     * {@inheritDoc}
     */
    public function hasRole(\Admin\Entity\Role $userRole): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'hasRole', [$userRole]);

        return parent::hasRole($userRole);
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): \Doctrine\Common\Collections\ArrayCollection|array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRoles', []);

        return parent::getRoles();
    }

    /**
     * {@inheritDoc}
     */
    public function setRoles($roles): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRoles', [$roles]);

        return parent::setRoles($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function isFunder(): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isFunder', []);

        return parent::isFunder();
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
    public function setId(int $id): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPassword', []);

        return parent::getPassword();
    }

    /**
     * {@inheritDoc}
     */
    public function setPassword(string $password): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPassword', [$password]);

        return parent::setPassword($password);
    }

    /**
     * {@inheritDoc}
     */
    public function getFirstName(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFirstName', []);

        return parent::getFirstName();
    }

    /**
     * {@inheritDoc}
     */
    public function setFirstName(string $firstName): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFirstName', [$firstName]);

        return parent::setFirstName($firstName);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastName(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLastName', []);

        return parent::getLastName();
    }

    /**
     * {@inheritDoc}
     */
    public function setLastName(string $lastName): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastName', [$lastName]);

        return parent::setLastName($lastName);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEmail', []);

        return parent::getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail(string $email): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEmail', [$email]);

        return parent::setEmail($email);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateCreated(): \DateTime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDateCreated', []);

        return parent::getDateCreated();
    }

    /**
     * {@inheritDoc}
     */
    public function setDateCreated(\DateTime $dateCreated): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateCreated', [$dateCreated]);

        return parent::setDateCreated($dateCreated);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastUpdate(): ?\DateTime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLastUpdate', []);

        return parent::getLastUpdate();
    }

    /**
     * {@inheritDoc}
     */
    public function setLastUpdate(?\DateTime $lastUpdate): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastUpdate', [$lastUpdate]);

        return parent::setLastUpdate($lastUpdate);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateEnd(): ?\DateTime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDateEnd', []);

        return parent::getDateEnd();
    }

    /**
     * {@inheritDoc}
     */
    public function setDateEnd(?\DateTime $dateEnd): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateEnd', [$dateEnd]);

        return parent::setDateEnd($dateEnd);
    }

    /**
     * {@inheritDoc}
     */
    public function getSession(): \Doctrine\Common\Collections\Collection|array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSession', []);

        return parent::getSession();
    }

    /**
     * {@inheritDoc}
     */
    public function setSession($session): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSession', [$session]);

        return parent::setSession($session);
    }

    /**
     * {@inheritDoc}
     */
    public function getOAuthAccessTokens(): \Doctrine\Common\Collections\Collection|array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOAuthAccessTokens', []);

        return parent::getOAuthAccessTokens();
    }

    /**
     * {@inheritDoc}
     */
    public function setOAuthAccessTokens($oAuthAccessTokens): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOAuthAccessTokens', [$oAuthAccessTokens]);

        return parent::setOAuthAccessTokens($oAuthAccessTokens);
    }

    /**
     * {@inheritDoc}
     */
    public function getOAuthAuthorizationCodes(): \Doctrine\Common\Collections\Collection|array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOAuthAuthorizationCodes', []);

        return parent::getOAuthAuthorizationCodes();
    }

    /**
     * {@inheritDoc}
     */
    public function setOAuthAuthorizationCodes($oAuthAuthorizationCodes): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOAuthAuthorizationCodes', [$oAuthAuthorizationCodes]);

        return parent::setOAuthAuthorizationCodes($oAuthAuthorizationCodes);
    }

    /**
     * {@inheritDoc}
     */
    public function getOAuthRefreshTokens(): \Doctrine\Common\Collections\ArrayCollection|array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOAuthRefreshTokens', []);

        return parent::getOAuthRefreshTokens();
    }

    /**
     * {@inheritDoc}
     */
    public function setOAuthRefreshTokens($oAuthRefreshTokens): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOAuthRefreshTokens', [$oAuthRefreshTokens]);

        return parent::setOAuthRefreshTokens($oAuthRefreshTokens);
    }

    /**
     * {@inheritDoc}
     */
    public function getFunder(): ?\Cluster\Entity\Funder
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFunder', []);

        return parent::getFunder();
    }

    /**
     * {@inheritDoc}
     */
    public function setFunder(?\Cluster\Entity\Funder $funder): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFunder', [$funder]);

        return parent::setFunder($funder);
    }

    /**
     * {@inheritDoc}
     */
    public function getOAuthJwt(): \Doctrine\Common\Collections\Collection|array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOAuthJwt', []);

        return parent::getOAuthJwt();
    }

    /**
     * {@inheritDoc}
     */
    public function setOAuthJwt(\Doctrine\Common\Collections\Collection|array $oAuthJwt): \Admin\Entity\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOAuthJwt', [$oAuthJwt]);

        return parent::setOAuthJwt($oAuthJwt);
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
