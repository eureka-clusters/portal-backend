<?php

namespace DoctrineORMModule\Proxy\__CG__\Mailing\Entity;


/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class EmailMessage extends \Mailing\Entity\EmailMessage implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'id', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'identifier', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'dateCreated', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'user', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'template', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'mailer', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'sender', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'emailAddress', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'subject', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'to', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'cc', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'bcc', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'message', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'amountOfAttachments', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'event', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'latestEvent', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'dateLatestEvent'];
        }

        return ['__isInitialized__', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'id', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'identifier', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'dateCreated', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'user', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'template', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'mailer', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'sender', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'emailAddress', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'subject', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'to', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'cc', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'bcc', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'message', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'amountOfAttachments', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'event', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'latestEvent', '' . "\0" . 'Mailing\\Entity\\EmailMessage' . "\0" . 'dateLatestEvent'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (EmailMessage $proxy) {
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
    public function setId(int $id): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdentifier', []);

        return parent::getIdentifier();
    }

    /**
     * {@inheritDoc}
     */
    public function setIdentifier(string $identifier): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIdentifier', [$identifier]);

        return parent::setIdentifier($identifier);
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
    public function setDateCreated(\DateTime $dateCreated): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateCreated', [$dateCreated]);

        return parent::setDateCreated($dateCreated);
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
    public function setUser(?\Admin\Entity\User $user): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUser', [$user]);

        return parent::setUser($user);
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
    public function setTemplate(\Mailing\Entity\Template $template): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTemplate', [$template]);

        return parent::setTemplate($template);
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
    public function setMailer(\Mailing\Entity\Mailer $mailer): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMailer', [$mailer]);

        return parent::setMailer($mailer);
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
    public function setSender(\Mailing\Entity\Sender $sender): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSender', [$sender]);

        return parent::setSender($sender);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmailAddress(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEmailAddress', []);

        return parent::getEmailAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function setEmailAddress(string $emailAddress): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEmailAddress', [$emailAddress]);

        return parent::setEmailAddress($emailAddress);
    }

    /**
     * {@inheritDoc}
     */
    public function getSubject(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSubject', []);

        return parent::getSubject();
    }

    /**
     * {@inheritDoc}
     */
    public function setSubject(string $subject): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSubject', [$subject]);

        return parent::setSubject($subject);
    }

    /**
     * {@inheritDoc}
     */
    public function getTo(): array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTo', []);

        return parent::getTo();
    }

    /**
     * {@inheritDoc}
     */
    public function setTo(array $to): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTo', [$to]);

        return parent::setTo($to);
    }

    /**
     * {@inheritDoc}
     */
    public function getCc(): ?array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCc', []);

        return parent::getCc();
    }

    /**
     * {@inheritDoc}
     */
    public function setCc(?array $cc): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCc', [$cc]);

        return parent::setCc($cc);
    }

    /**
     * {@inheritDoc}
     */
    public function getBcc(): ?array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBcc', []);

        return parent::getBcc();
    }

    /**
     * {@inheritDoc}
     */
    public function setBcc(?array $bcc): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBcc', [$bcc]);

        return parent::setBcc($bcc);
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMessage', []);

        return parent::getMessage();
    }

    /**
     * {@inheritDoc}
     */
    public function setMessage(string $message): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMessage', [$message]);

        return parent::setMessage($message);
    }

    /**
     * {@inheritDoc}
     */
    public function getAmountOfAttachments(): int
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAmountOfAttachments', []);

        return parent::getAmountOfAttachments();
    }

    /**
     * {@inheritDoc}
     */
    public function setAmountOfAttachments(int $amountOfAttachments): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAmountOfAttachments', [$amountOfAttachments]);

        return parent::setAmountOfAttachments($amountOfAttachments);
    }

    /**
     * {@inheritDoc}
     */
    public function getEvent(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEvent', []);

        return parent::getEvent();
    }

    /**
     * {@inheritDoc}
     */
    public function setEvent(\Doctrine\Common\Collections\Collection $event): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEvent', [$event]);

        return parent::setEvent($event);
    }

    /**
     * {@inheritDoc}
     */
    public function getLatestEvent(): ?string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLatestEvent', []);

        return parent::getLatestEvent();
    }

    /**
     * {@inheritDoc}
     */
    public function setLatestEvent(?string $latestEvent): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLatestEvent', [$latestEvent]);

        return parent::setLatestEvent($latestEvent);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateLatestEvent(): ?\DateTime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDateLatestEvent', []);

        return parent::getDateLatestEvent();
    }

    /**
     * {@inheritDoc}
     */
    public function setDateLatestEvent(?\DateTime $dateLatestEvent): \Mailing\Entity\EmailMessage
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateLatestEvent', [$dateLatestEvent]);

        return parent::setDateLatestEvent($dateLatestEvent);
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
