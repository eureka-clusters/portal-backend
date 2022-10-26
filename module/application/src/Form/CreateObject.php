<?php

declare(strict_types=1);

namespace Application\Form;

use Application\Entity\AbstractEntity;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Psr\Container\ContainerInterface;

use function _;
use function class_exists;

class CreateObject extends Form
{
    public function __construct(
        EntityManager $entityManager,
        AbstractEntity $object,
        ContainerInterface $container
    ) {
        parent::__construct(name: $object->get(switch: "entity_name"));

        /**
         * There is an option to drag the fieldset from the container,
         * We then need to check if if an factory is present,
         * If not we will use the default ObjectFieldset
         */

        $objectSpecificFieldset = $object->get(switch: 'entity_fieldset_name');
        /**
         * Load a specific fieldSet when present
         */
        if ($container->has($objectSpecificFieldset)) {
            $objectFieldset = $container->get($objectSpecificFieldset);
        } elseif (class_exists(class: $objectSpecificFieldset)) {
            $objectFieldset = new $objectSpecificFieldset($entityManager, $object);
        } else {
            $objectFieldset = new ObjectFieldset(entityManager: $entityManager, object: $object);
        }
        $objectFieldset->setUseAsBaseFieldset(useAsBaseFieldset: true);
        $this->add(elementOrFieldset: $objectFieldset);

        $this->setAttribute(key: 'method', value: 'post');
        $this->setAttribute(key: 'class', value: 'form-horizontal');

        $this->add(
            elementOrFieldset: [
                'type' => Csrf::class,
                'name' => 'csrf',
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit"),
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'delete',
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-delete"),
                ],
            ]
        );
        $this->add(
            elementOrFieldset: [
                'type'       => Submit::class,
                'name'       => 'restore',
                'attributes' => [
                    'class' => "btn btn-info",
                    'value' => _("txt-restore"),
                ],
            ]
        );
    }
}
