<?php

namespace DoctrineORMModule\Proxy\__CG__\Cluster\Entity;


/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Country extends \Cluster\Entity\Country implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'id', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'cd', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'country', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'docRef', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'iso3', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'numcode', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'funder', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'organisations', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'evaluation'];
        }

        return ['__isInitialized__', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'id', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'cd', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'country', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'docRef', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'iso3', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'numcode', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'funder', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'organisations', '' . "\0" . 'Cluster\\Entity\\Country' . "\0" . 'evaluation'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Country $proxy) {
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
    public function setId(?int $id): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getCd(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCd', []);

        return parent::getCd();
    }

    /**
     * {@inheritDoc}
     */
    public function setCd(string $cd): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCd', [$cd]);

        return parent::setCd($cd);
    }

    /**
     * {@inheritDoc}
     */
    public function getCountry(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCountry', []);

        return parent::getCountry();
    }

    /**
     * {@inheritDoc}
     */
    public function setCountry(string $country): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCountry', [$country]);

        return parent::setCountry($country);
    }

    /**
     * {@inheritDoc}
     */
    public function getDocRef(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDocRef', []);

        return parent::getDocRef();
    }

    /**
     * {@inheritDoc}
     */
    public function setDocRef(string $docRef): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDocRef', [$docRef]);

        return parent::setDocRef($docRef);
    }

    /**
     * {@inheritDoc}
     */
    public function getIso3(): ?string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIso3', []);

        return parent::getIso3();
    }

    /**
     * {@inheritDoc}
     */
    public function setIso3(?string $iso3): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIso3', [$iso3]);

        return parent::setIso3($iso3);
    }

    /**
     * {@inheritDoc}
     */
    public function getNumcode(): int
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNumcode', []);

        return parent::getNumcode();
    }

    /**
     * {@inheritDoc}
     */
    public function setNumcode(int $numcode): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNumcode', [$numcode]);

        return parent::setNumcode($numcode);
    }

    /**
     * {@inheritDoc}
     */
    public function getFunder(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFunder', []);

        return parent::getFunder();
    }

    /**
     * {@inheritDoc}
     */
    public function setFunder($funder): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFunder', [$funder]);

        return parent::setFunder($funder);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrganisations(): \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrganisations', []);

        return parent::getOrganisations();
    }

    /**
     * {@inheritDoc}
     */
    public function setOrganisations($organisations): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrganisations', [$organisations]);

        return parent::setOrganisations($organisations);
    }

    /**
     * {@inheritDoc}
     */
    public function getEvaluation(): \Doctrine\Common\Collections\Collection
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEvaluation', []);

        return parent::getEvaluation();
    }

    /**
     * {@inheritDoc}
     */
    public function setEvaluation(\Doctrine\Common\Collections\Collection $evaluation): \Cluster\Entity\Country
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEvaluation', [$evaluation]);

        return parent::setEvaluation($evaluation);
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
