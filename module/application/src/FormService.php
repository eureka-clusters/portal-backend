<?php

declare(strict_types=1);

namespace Application;

use Application\Entity\AbstractEntity;
use Application\Form\CreateObject;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Psr\Container\ContainerInterface;

use function class_exists;

class FormService
{
    public function __construct(protected ContainerInterface $container, protected EntityManager $entityManager)
    {
    }

    public function prepare($classNameOrEntity, array $data = [], array $options = []): Form
    {
        /**
         * The form can be created from an empty element, we then expect the $formClassName to be filled
         * This should be a string, indicating the class
         *
         * But if the class a class is injected, we will change it into the className but hint the user to use a string
         */
        if (!$classNameOrEntity instanceof AbstractEntity) {
            $classNameOrEntity = new $classNameOrEntity();
        }

        $form = $this->getForm($classNameOrEntity, $options);
        $form->setData($data);

        return $form;
    }

    private function getForm(AbstractEntity $class, array $options = []): Form
    {
        $formName = $class->get('entity_form_name');
        $filterName = $class->get('entity_inputfilter_name');

        /**
         * The filter and the form can dynamically be created by pulling the form from the serviceManager
         * if the form or filter is not give in the serviceManager we will create it by default
         */
        if (!$this->container->has($formName)) {
            $form = new CreateObject($this->entityManager, $class, $this->container);
        } else {
            $form = $this->container->get($formName);
        }

        if ($this->container->has($filterName)) {
            /** @var InputFilter $filter */
            $filter = $this->container->get($filterName);
            $form->setInputFilter($filter);
        } elseif (class_exists($filterName)) {
            $form->setInputFilter(new $filterName($this->entityManager, $class));
        }

        $form->bind($class);

        $form->setAttribute('class', 'form-horizontal');
        $form->setAttribute('action', '');

        return $form;
    }
}
