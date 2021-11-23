<?php

namespace DoctrineORMModule\Proxy\__CG__\Cluster\Entity\Project;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Partner extends \Cluster\Entity\Project\Partner implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'id', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'organisation', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'project', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'slug', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'organisationName', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'projectName', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'isActive', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'isCoordinator', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'isSelfFunded', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'technicalContact', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'costsAndEffort'];
        }

        return ['__isInitialized__', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'id', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'organisation', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'project', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'slug', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'organisationName', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'projectName', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'isActive', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'isCoordinator', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'isSelfFunded', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'technicalContact', '' . "\0" . 'Cluster\\Entity\\Project\\Partner' . "\0" . 'costsAndEffort'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Partner $proxy) {
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
    public function getProject(): \Cluster\Entity\Project
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProject', []);

        return parent::getProject();
    }

    /**
     * {@inheritDoc}
     */
    public function setProject(\Cluster\Entity\Project $project): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProject', [$project]);

        return parent::setProject($project);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrganisation(): \Cluster\Entity\Organisation
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrganisation', []);

        return parent::getOrganisation();
    }

    /**
     * {@inheritDoc}
     */
    public function setOrganisation(\Cluster\Entity\Organisation $organisation): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrganisation', [$organisation]);

        return parent::setOrganisation($organisation);
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
    public function setId(int $id): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getSlug(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSlug', []);

        return parent::getSlug();
    }

    /**
     * {@inheritDoc}
     */
    public function setSlug(string $slug): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSlug', [$slug]);

        return parent::setSlug($slug);
    }

    /**
     * {@inheritDoc}
     */
    public function isActive(): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isActive', []);

        return parent::isActive();
    }

    /**
     * {@inheritDoc}
     */
    public function setIsActive(bool $isActive): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsActive', [$isActive]);

        return parent::setIsActive($isActive);
    }

    /**
     * {@inheritDoc}
     */
    public function isCoordinator(): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isCoordinator', []);

        return parent::isCoordinator();
    }

    /**
     * {@inheritDoc}
     */
    public function setIsCoordinator(bool $isCoordinator): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsCoordinator', [$isCoordinator]);

        return parent::setIsCoordinator($isCoordinator);
    }

    /**
     * {@inheritDoc}
     */
    public function isSelfFunded(): bool
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isSelfFunded', []);

        return parent::isSelfFunded();
    }

    /**
     * {@inheritDoc}
     */
    public function setIsSelfFunded(bool $isSelfFunded): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsSelfFunded', [$isSelfFunded]);

        return parent::setIsSelfFunded($isSelfFunded);
    }

    /**
     * {@inheritDoc}
     */
    public function getTechnicalContact(): array
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTechnicalContact', []);

        return parent::getTechnicalContact();
    }

    /**
     * {@inheritDoc}
     */
    public function setTechnicalContact(array $technicalContact): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTechnicalContact', [$technicalContact]);

        return parent::setTechnicalContact($technicalContact);
    }

    /**
     * {@inheritDoc}
     */
    public function getCostsAndEffort()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCostsAndEffort', []);

        return parent::getCostsAndEffort();
    }

    /**
     * {@inheritDoc}
     */
    public function setCostsAndEffort($costsAndEffort): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCostsAndEffort', [$costsAndEffort]);

        return parent::setCostsAndEffort($costsAndEffort);
    }

    /**
     * {@inheritDoc}
     */
    public function setOrganisationName(string $organisationName): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrganisationName', [$organisationName]);

        return parent::setOrganisationName($organisationName);
    }

    /**
     * {@inheritDoc}
     */
    public function setProjectName(string $projectName): \Cluster\Entity\Project\Partner
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProjectName', [$projectName]);

        return parent::setProjectName($projectName);
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
