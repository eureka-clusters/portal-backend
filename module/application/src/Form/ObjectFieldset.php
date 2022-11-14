<?php

declare(strict_types=1);

namespace Application\Form;

use Application\Entity\AbstractEntity;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Form\Element\ObjectRadio;
use DoctrineModule\Form\Element\ObjectSelect;
use Laminas\Form\Annotation\AttributeBuilder;
use Laminas\Form\Element;
use Laminas\Form\Element\Collection;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Fieldset;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;

use function array_key_exists;
use function array_merge;
use function asort;
use function sprintf;
use function ucfirst;

class ObjectFieldset extends Fieldset
{
    public function __construct(private readonly EntityManager $entityManager, AbstractEntity $object)
    {
        parent::__construct(name: $object->get(switch: 'underscore_entity_name'));
        $doctrineHydrator = new DoctrineObject(objectManager: $entityManager);
        $this->setHydrator(hydrator: $doctrineHydrator)->setObject(object: $object);
        $builder = new AttributeBuilder();
        // createForm() already creates a proper form, so attaching its elements
        // to $this is only for backward compatibility
        $data = $builder->createForm(entity: $object);
        $this->addElements(dataFieldset: $data, object: $object, baseFieldset: $this);
    }

    protected function addElements(
        FormInterface|FieldsetInterface $dataFieldset,
        ?AbstractEntity $object,
        ?Fieldset $baseFieldset = null
    ): void {
        /** @var Element $element */
        foreach ($dataFieldset->getElements() as $element) {
            $this->parseElement(element: $element, object: $object);
            // Add only when a type is provided
            if (! array_key_exists(key: 'type', array: $element->getAttributes())) {
                continue;
            }

            if ($baseFieldset instanceof Fieldset) {
                $baseFieldset->add(elementOrFieldset: $element);
            } else {
                $dataFieldset->add(elementOrFieldset: $element);
            }
        }
        // Prepare the target element of a form collection
        if ($dataFieldset instanceof Collection) {
            $targetFieldset = $dataFieldset->getTargetElement();
            // Collections have "container" fieldsets for their items, they must have the hydrator set too
            if ($targetFieldset instanceof FieldsetInterface) {
                $targetFieldset->setHydrator(hydrator: $this->getHydrator());
            }
            /** @var Element $element */
            foreach ($targetFieldset->getElements() as $element) {
                $this->parseElement(element: $element, object: $targetFieldset->getObject());
            }
        }

        foreach ($dataFieldset->getFieldsets() as $subFieldset) {
            /** @var Fieldset $subFieldset */
            $subFieldset->setHydrator(hydrator: $this->getHydrator());
            $this->addElements(dataFieldset: $subFieldset, object: $subFieldset->getObject());
            $this->add(elementOrFieldset: $subFieldset);
        }
    }

    protected function parseElement(Element $element, ?AbstractEntity $object): void
    {
        // Go over each element to add the objectManager to the EntitySelect
        /** Element $element */
        if (
            $element instanceof ObjectSelect ||
            $element instanceof ObjectMultiCheckbox ||
            $element instanceof ObjectRadio
        ) {
            $element->setOptions(
                options: array_merge(
                    $element->getOptions(),
                    ['object_manager' => $this->entityManager]
                )
            );
        }

        if (($element instanceof Radio || $element instanceof Select || $element instanceof MultiCheckbox) && ! $element instanceof ObjectRadio) {
            $attributes = $element->getAttributes();

            if (isset($attributes['array'])) {
                $valueOptionsArray = sprintf('get%s', ucfirst(string: (string) $attributes['array']));

                $values = $object::$valueOptionsArray();

                asort(array: $values);

                $element->setOptions(
                    options: array_merge(
                        $element->getOptions(),
                        ['value_options' => $values]
                    )
                );
            }
        }
    }
}
