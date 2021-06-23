<?php

namespace DoctrineORMModule\Proxy\__CG__\Cluster\Entity\Statistics;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Partner extends \Cluster\Entity\Statistics\Partner implements \Doctrine\ORM\Proxy\Proxy
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
     * {@inheritDoc}
     * @param string $name
     */
    public function __get($name)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__get', [$name]);
        return parent::__get($name);
    }

    /**
     * {@inheritDoc}
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value): void
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__set', [$name, $value]);
        parent::__set($name, $value);
        return;
    }

    /**
     * {@inheritDoc}
     * @param  string $name
     * @return boolean
     */
    public function __isset($name): bool
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__isset', [$name]);

        return parent::__isset($name);
    }

    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'id', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'identifier', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectNumber', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectName', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectTitle', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectDescription', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'technicalArea', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'programme', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'programmeCall', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'primaryCluster', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'secondaryCluster', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'labelDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'cancelDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'officialStartDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'officialEndDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectStatus', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectLeader', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'partner', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'partnerIdentifier', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'country', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'partnerType', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'active', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'coordinator', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'selfFunded', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'technicalContact', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'year', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poSubmissionDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poStatus', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poTotalEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poTotalCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poEffortInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poCostsInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poCountries', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppSubmissionDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppStatus', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppTotalEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppTotalCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppEffortInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppCostsInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppCountries', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionSubmissionDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionStatus', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionType', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionTotalEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionTotalCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionEffortInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionCostsInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionCountries'];
        }

        return ['__isInitialized__', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'id', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'identifier', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectNumber', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectName', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectTitle', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectDescription', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'technicalArea', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'programme', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'programmeCall', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'primaryCluster', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'secondaryCluster', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'labelDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'cancelDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'officialStartDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'officialEndDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectStatus', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'projectLeader', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'partner', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'partnerIdentifier', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'country', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'partnerType', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'active', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'coordinator', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'selfFunded', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'technicalContact', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'year', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poSubmissionDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poStatus', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poTotalEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poTotalCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poEffortInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poCostsInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'poCountries', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppSubmissionDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppStatus', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppTotalEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppTotalCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppEffortInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppCostsInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'fppCountries', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionSubmissionDate', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionStatus', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionType', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionTotalEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionTotalCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionEffort', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionCosts', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionEffortInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionCostsInYear', '' . "\0" . 'Cluster\\Entity\\Statistics\\Partner' . "\0" . 'latestVersionCountries'];
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

    
}