<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\External;

use Quillstack\DI\Container;
use Quillstack\DI\CustomFactoryInterface;

final class ExternalInstanceFactory implements CustomFactoryInterface
{
    private Container $container;

    /**
     * Filled from the configuration, which is what a factory built through the container
     * can be told.
     */
    public string $name = 'default';

    public function create(string $id): object
    {
        $external = new $id();
        $external->test = 'test';

        return $external;
    }

    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }
}
