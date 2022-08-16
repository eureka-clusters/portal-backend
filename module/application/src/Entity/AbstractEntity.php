<?php

declare(strict_types=1);

namespace Application\Entity;

use InvalidArgumentException;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Stringable;

use function array_slice;
use function explode;
use function implode;
use function is_array;
use function method_exists;
use function sprintf;
use function str_replace;
use function strtolower;
use function ucfirst;

abstract class AbstractEntity implements EntityInterface, ResourceInterface, Stringable
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
        return match ($switch) {
            'class_name', 'full_entity_name' => str_replace('DoctrineORMModule\Proxy\__CG__\\', '', static::class),
            'namespace' => implode('', array_slice(explode('\\', $this->get('class_name')), 0, 1)),
            'entity_name' => implode('', array_slice(explode('\\', $this->get('class_name')), -1)),
            'underscore_entity_name' => strtolower(implode('_', explode('\\', $this->get('class_name')))),
            'entity_fieldset_name' => sprintf(
                '%sFieldset',
                str_replace(['Entity\\', 'Entity'], ['Form\\', ''], $this->get('class_name'))
            ),
            'entity_form_name' => sprintf(
                '%sForm',
                str_replace('Entity\\', 'Form\\', $this->get('class_name'))
            ),
            'entity_inputfilter_name' => sprintf(
                '%sFilter',
                str_replace(['Entity\\', 'Entity'], ['InputFilter\\', ''], $this->get('class_name'))
            ),
            default => throw new InvalidArgumentException(sprintf('Unknown option %s for get entity name', $switch)),
        };
    }

    public function isEmpty(): bool
    {
        return null === $this->getId();
    }

    public function has(string $prop): bool
    {
        $getter = 'get' . ucfirst($prop);
        if (method_exists($this, $getter)) {
            if (str_contains($prop, 's') && is_array($this->$getter())) {
                return true;
            }

            if ($this->$getter()) {
                return true;
            }
        }

        return false;
    }
}
