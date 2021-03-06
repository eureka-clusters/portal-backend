<?php

namespace DoctrineORMModule\Proxy\__CG__\Admin\Entity\Api;


/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Log extends \Admin\Entity\Api\Log implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'id', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'class', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'type', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'dateCreated', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'payload', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'statusCode', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'status', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'response'];
        }

        return ['__isInitialized__', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'id', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'class', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'type', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'dateCreated', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'payload', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'statusCode', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'status', '' . "\0" . 'Admin\\Entity\\Api\\Log' . "\0" . 'response'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Log $proxy) {
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
    public function getTypeText(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTypeText', []);

        return parent::getTypeText();
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
    public function setId(int $id): \Admin\Entity\Api\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClass', []);

        return parent::getClass();
    }

    /**
     * {@inheritDoc}
     */
    public function setClass(string $class): \Admin\Entity\Api\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClass', [$class]);

        return parent::setClass($class);
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): int
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getType', []);

        return parent::getType();
    }

    /**
     * {@inheritDoc}
     */
    public function setType(int $type): \Admin\Entity\Api\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setType', [$type]);

        return parent::setType($type);
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
    public function setDateCreated(\DateTime $dateCreated): \Admin\Entity\Api\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateCreated', [$dateCreated]);

        return parent::setDateCreated($dateCreated);
    }

    /**
     * {@inheritDoc}
     */
    public function getPayload(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPayload', []);

        return parent::getPayload();
    }

    /**
     * {@inheritDoc}
     */
    public function setPayload(string $payload): \Admin\Entity\Api\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPayload', [$payload]);

        return parent::setPayload($payload);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusCode(): int
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatusCode', []);

        return parent::getStatusCode();
    }

    /**
     * {@inheritDoc}
     */
    public function setStatusCode(int $statusCode): \Admin\Entity\Api\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatusCode', [$statusCode]);

        return parent::setStatusCode($statusCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', []);

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus(string $status): \Admin\Entity\Api\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', [$status]);

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function getResponse(): ?string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getResponse', []);

        return parent::getResponse();
    }

    /**
     * {@inheritDoc}
     */
    public function setResponse(?string $response): \Admin\Entity\Api\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setResponse', [$response]);

        return parent::setResponse($response);
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
