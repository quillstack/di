<?php

declare(strict_types=1);

namespace Quillstack\DI\InstanceFactories;

use Quillstack\DI\Container;
use Quillstack\DI\InstanceFactoryWithContainerInterface;

/**
 * The factory creates the instance of the class based on the interface. It requires the interface and the class
 * chosen for this interface are set in the DI Container configuration.
 */
class ClassFromInterfaceFactory implements InstanceFactoryWithContainerInterface
{
    /**
     * The instance of Container class.
     *
     * @var Container
     */
    private Container $container;

    /**
     * Sets the instance of the Container class.
     *
     * @param Container $container
     *
     * @return ClassFromInterfaceFactory
     */
    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $id): object
    {
        return $this->container->get(
            $this->container->getInstantiableClassForInterface($id)
        );
    }
}
