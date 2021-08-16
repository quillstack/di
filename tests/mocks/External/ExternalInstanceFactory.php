<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\External;

use Quillstack\DI\Container;
use Quillstack\DI\CustomFactoryInterface;

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
