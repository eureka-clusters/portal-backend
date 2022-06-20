<?php

namespace DoctrineORMModule\Proxy\__CG__\Admin\Entity;


/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Log extends \Admin\Entity\Log implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'id', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'date', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'type', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'event', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'url', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'file', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'line', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'errorType', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'trace', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'requestData'];
        }

        return ['__isInitialized__', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'id', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'date', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'type', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'event', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'url', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'file', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'line', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'errorType', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'trace', '' . "\0" . 'Admin\\Entity\\Log' . "\0" . 'requestData'];
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
    public function __toString(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', []);

        return parent::__toString();
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
    public function setId(?int $id): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getDate(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDate', []);

        return parent::getDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setDate(string $date): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDate', [$date]);

        return parent::setDate($date);
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
    public function setType(int $type): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setType', [$type]);

        return parent::setType($type);
    }

    /**
     * {@inheritDoc}
     */
    public function getEvent(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEvent', []);

        return parent::getEvent();
    }

    /**
     * {@inheritDoc}
     */
    public function setEvent(string $event): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEvent', [$event]);

        return parent::setEvent($event);
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUrl', []);

        return parent::getUrl();
    }

    /**
     * {@inheritDoc}
     */
    public function setUrl(string $url): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUrl', [$url]);

        return parent::setUrl($url);
    }

    /**
     * {@inheritDoc}
     */
    public function getFile(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFile', []);

        return parent::getFile();
    }

    /**
     * {@inheritDoc}
     */
    public function setFile(string $file): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFile', [$file]);

        return parent::setFile($file);
    }

    /**
     * {@inheritDoc}
     */
    public function getLine(): int
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLine', []);

        return parent::getLine();
    }

    /**
     * {@inheritDoc}
     */
    public function setLine(int $line): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLine', [$line]);

        return parent::setLine($line);
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorType(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getErrorType', []);

        return parent::getErrorType();
    }

    /**
     * {@inheritDoc}
     */
    public function setErrorType(string $errorType): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setErrorType', [$errorType]);

        return parent::setErrorType($errorType);
    }

    /**
     * {@inheritDoc}
     */
    public function getTrace(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTrace', []);

        return parent::getTrace();
    }

    /**
     * {@inheritDoc}
     */
    public function setTrace(string $trace): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTrace', [$trace]);

        return parent::setTrace($trace);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestData(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRequestData', []);

        return parent::getRequestData();
    }

    /**
     * {@inheritDoc}
     */
    public function setRequestData(string $requestData): \Admin\Entity\Log
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRequestData', [$requestData]);

        return parent::setRequestData($requestData);
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
