<?php

declare(strict_types=1);

namespace Quillstack\DI;

interface InstanceFactoryWithContainerInterface extends InstanceFactoryInterface
{
    /**
     * Sets the instance of the Container class.
     *
     * @param Container $container
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function setContainer(Container $container): self;
}
