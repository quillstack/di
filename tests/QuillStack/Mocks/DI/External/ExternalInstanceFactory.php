<?php

declare(strict_types=1);

namespace QuillStack\Mocks\DI\External;

use QuillStack\DI\Container;
use QuillStack\DI\CustomFactoryInterface;
use QuillStack\DI\InstanceFactoryWithContainerInterface;

final class ExternalInstanceFactory implements CustomFactoryInterface
{
    /**
     * @var Container
     */
    private Container $container;

    public function create(string $id)
    {
        $external = new $id;
        $external->test = 'test';

        return $external;
    }

    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }
}
