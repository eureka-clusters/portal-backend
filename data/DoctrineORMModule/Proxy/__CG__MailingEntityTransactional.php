<?php

namespace DoctrineORMModule\Proxy\__CG__\Mailing\Entity;


/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Transactional extends \Mailing\Entity\Transactional implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'id', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'name', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'key', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'dateCreated', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'lastUpdate', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'mailHtml', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'mailSubject', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'template', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'sender', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'mailer'];
        }

        return ['__isInitialized__', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'id', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'name', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'key', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'dateCreated', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'lastUpdate', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'mailHtml', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'mailSubject', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'template', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'sender', '' . "\0" . 'Mailing\\Entity\\Transactional' . "\0" . 'mailer'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Transactional $proxy) {
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
    public function parseSourceContent(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'parseSourceContent', []);

        return parent::parseSourceContent();
    }

    /**
     * {@inheritDoc}
     */
    public function parseName(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'parseName', []);

        return parent::parseName();
    }

    /**
     * {@inheritDoc}
     */
    public function isLocked(): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isLocked', []);

        return parent::isLocked();
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
    public function setId(?int $id): \Mailing\Entity\Transactional
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
    public function setName(string $name): \Mailing\Entity\Transactional
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$name]);

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getKey(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getKey', []);

        return parent::getKey();
    }

    /**
     * {@inheritDoc}
     */
    public function setKey(string $key): \Mailing\Entity\Transactional
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setKey', [$key]);

        return parent::setKey($key);
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
    public function setDateCreated(\DateTime $dateCreated): \Mailing\Entity\Transactional
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
    public function setLastUpdate(?\DateTime $lastUpdate): \Mailing\Entity\Transactional
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastUpdate', [$lastUpdate]);

        return parent::setLastUpdate($lastUpdate);
    }

    /**
     * {@inheritDoc}
     */
    public function getMailHtml(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMailHtml', []);

        return parent::getMailHtml();
    }

    /**
     * {@inheritDoc}
     */
    public function setMailHtml(string $mailHtml): \Mailing\Entity\Transactional
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMailHtml', [$mailHtml]);

        return parent::setMailHtml($mailHtml);
    }

    /**
     * {@inheritDoc}
     */
    public function getMailSubject(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMailSubject', []);

        return parent::getMailSubject();
    }

    /**
     * {@inheritDoc}
     */
    public function setMailSubject(string $mailSubject): \Mailing\Entity\Transactional
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMailSubject', [$mailSubject]);

        return parent::setMailSubject($mailSubject);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplate(): \Mailing\Entity\Template
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTemplate', []);

        return parent::getTemplate();
    }

    /**
     * {@inheritDoc}
     */
    public function setTemplate(\Mailing\Entity\Template $template): \Mailing\Entity\Transactional
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTemplate', [$template]);

        return parent::setTemplate($template);
    }

    /**
     * {@inheritDoc}
     */
    public function getSender(): \Mailing\Entity\Sender
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSender', []);

        return parent::getSender();
    }

    /**
     * {@inheritDoc}
     */
    public function setSender(\Mailing\Entity\Sender $sender): \Mailing\Entity\Transactional
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSender', [$sender]);

        return parent::setSender($sender);
    }

    /**
     * {@inheritDoc}
     */
    public function getMailer(): \Mailing\Entity\Mailer
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMailer', []);

        return parent::getMailer();
    }

    /**
     * {@inheritDoc}
     */
    public function setMailer(\Mailing\Entity\Mailer $mailer): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMailer', [$mailer]);

        parent::setMailer($mailer);
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