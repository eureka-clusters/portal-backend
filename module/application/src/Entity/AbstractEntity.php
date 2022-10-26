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
        return sprintf('%s-%s', $this->get(switch: 'underscore_entity_name'), (string)$this->getId());
    }

    public function get(string $switch): string
    {
        return match ($switch) {
            'class_name', 'full_entity_name' => str_replace(
                search: 'DoctrineORMModule\Proxy\__CG__\\',
                replace: '',
                subject: static::class),
            'namespace' => implode(
                separator: '',
                array: array_slice(
                array: explode(
                separator: '\\',
                string: $this->get(
                switch: 'class_name')),
                offset: 0,
                length: 1)),
            'entity_name' => implode(
                separator: '',
                array: array_slice(
                array: explode(
                separator: '\\',
                string: $this->get(
                switch: 'class_name')),
                offset: -1)),
            'underscore_entity_name' => strtolower(
                string: implode(
                separator: '_',
                array: explode(
                separator: '\\',
                string: $this->get(switch: 'class_name')))),
            'entity_fieldset_name' => sprintf(
                '%sFieldset',
                str_replace(search: ['Entity\\', 'Entity'],
                    replace: ['Form\\', ''],
                    subject: $this->get(
                        switch: 'class_name'))
            ),
            'entity_form_name' => sprintf(
                '%sForm',
                str_replace(search: 'Entity\\', replace: 'Form\\', subject: $this->get(switch: 'class_name'))
            ),
            'entity_inputfilter_name' => sprintf(
                '%sFilter',
                str_replace(search: ['Entity\\', 'Entity'],
                    replace: ['InputFilter\\', ''],
                    subject: $this->get(
                        switch: 'class_name'))
            ),
            default => throw new InvalidArgumentException(message: sprintf('Unknown option %s for get entity name', $switch)),
        };
    }

    public function isEmpty(): bool
    {
        return null === $this->getId();
    }

    public function has(string $prop): bool
    {
        $getter = 'get' . ucfirst(string: $prop);
        if (method_exists(object_or_class: $this, method: $getter)) {
            if (str_contains(haystack: $prop, needle: 's') && is_array(value: $this->$getter())) {
                return true;
            }

            if ($this->$getter()) {
                return true;
            }
        }

        return false;
    }
}
