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
use function sprintf;
use function ucfirst;

class ObjectFieldset extends Fieldset
{
    public function __construct(private readonly EntityManager $entityManager, AbstractEntity $object)
    {
        parent::__construct($object->get('underscore_entity_name'));
        $doctrineHydrator = new DoctrineObject($entityManager);
        $this->setHydrator($doctrineHydrator)->setObject($object);
        $builder = new AttributeBuilder();
        // createForm() already creates a proper form, so attaching its elements
        // to $this is only for backward compatibility
        $data = $builder->createForm($object);
        $this->addElements($data, $object, $this);
    }

    protected function addElements(
        FormInterface|FieldsetInterface $dataFieldset,
        ?AbstractEntity $object,
        ?Fieldset $baseFieldset = null
    ): void {
        /** @var Element $element */
        foreach ($dataFieldset->getElements() as $element) {
            $this->parseElement($element, $object);
            // Add only when a type is provided
            if (!array_key_exists('type', $element->getAttributes())) {
                continue;
            }

            if ($baseFieldset instanceof Fieldset) {
                $baseFieldset->add($element);
            } else {
                $dataFieldset->add($element);
            }
        }
        // Prepare the target element of a form collection
        if ($dataFieldset instanceof Collection) {
            $targetFieldset = $dataFieldset->getTargetElement();
            // Collections have "container" fieldsets for their items, they must have the hydrator set too
            if ($targetFieldset instanceof FieldsetInterface) {
                $targetFieldset->setHydrator($this->getHydrator());
            }
            /** @var Element $element */
            foreach ($targetFieldset->getElements() as $element) {
                $this->parseElement($element, $targetFieldset->getObject());
            }
        }

        foreach ($dataFieldset->getFieldsets() as $subFieldset) {
            /** @var Fieldset $subFieldset */
            $subFieldset->setHydrator($this->getHydrator());
            $this->addElements($subFieldset, $subFieldset->getObject());
            $this->add($subFieldset);
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
                array_merge(
                    $element->getOptions(),
                    ['object_manager' => $this->entityManager]
                )
            );
        }

        if (($element instanceof Radio || $element instanceof Select || $element instanceof MultiCheckbox) && !$element instanceof ObjectRadio) {
            $attributes = $element->getAttributes();

            if (isset($attributes['array'])) {
                $valueOptionsArray = sprintf('get%s', ucfirst((string)$attributes['array']));

                $values = $object::$valueOptionsArray();

                asort($values);

                $element->setOptions(
                    array_merge(
                        $element->getOptions(),
                        ['value_options' => $values]
                    )
                );
            }
        }
    }
}
