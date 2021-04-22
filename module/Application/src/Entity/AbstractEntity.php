<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Application\Entity;

use InvalidArgumentException;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

use function array_slice;
use function explode;
use function implode;
use function is_array;
use function sprintf;
use function str_replace;

/**
 * Class AbstractEntity
 *
 * @package Application\Entity
 */
abstract class AbstractEntity implements EntityInterface, ResourceInterface
{
    public function __toString(): string
    {
        return $this->getResourceId();
    }

    public function getResourceId(): string
    {
        return sprintf('%s-%s', $this->get('underscore_entity_name'), (string)$this->getId());
    }

    public function get(string $switch): string
    {
        switch ($switch) {
            case 'class_name':
            case 'full_entity_name':
                return str_replace('DoctrineORMModule\Proxy\__CG__\\', '', static::class);
            case 'namespace':
                return implode('', array_slice(explode('\\', $this->get('class_name')), 0, 1));
            case 'entity_name':
                return implode('', array_slice(explode('\\', $this->get('class_name')), -1));
            case 'underscore_entity_name':
                return strtolower(implode('_', explode('\\', $this->get('class_name'))));
            case 'entity_fieldset_name':
                return sprintf(
                    '%sFieldset',
                    str_replace(['Entity\\','Entity'], ['Form\\',''], $this->get('class_name'))
                ); //Run\Form\RunFieldset
            case 'entity_form_name':
                return sprintf(
                    '%sForm',
                    str_replace('Entity\\', 'Form\\', $this->get('class_name'))
                ); //Run\Form\RunForm
            case 'entity_inputfilter_name':
                return sprintf(
                    '%sFilter',
                    str_replace(['Entity\\','Entity'], ['InputFilter\\',''], $this->get('class_name'))
                ); //Run\InputFilter\RunFilter
            default:
                throw new InvalidArgumentException(sprintf('Unknown option %s for get entity name', $switch));
        }
    }

    public function isEmpty(): bool
    {
        return null === $this->getId();
    }

    public function has(string $prop): bool
    {
        $getter = 'get' . ucfirst($prop);
        if (method_exists($this, $getter)) {
            if (strpos($prop, 's') !== false && is_array($this->$getter())) {
                return true;
            }

            if ($this->$getter()) {
                return true;
            }
        }

        return false;
    }
}
