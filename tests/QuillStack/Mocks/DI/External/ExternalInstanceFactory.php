<?php

namespace QuillStack\Mocks\DI\External;

use QuillStack\DI\Container;
use QuillStack\DI\CustomFactoryInterface;

final class ExternalInstanceFactory implements CustomFactoryInterface
{
    private Container $container;

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
