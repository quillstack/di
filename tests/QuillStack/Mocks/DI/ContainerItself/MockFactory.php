<?php

namespace QuillStack\Mocks\DI\ContainerItself;

use QuillStack\DI\Container;
use QuillStack\Mocks\DI\Simple\MockController;

final class MockFactory
{
    public function __construct(public Container $container)
    {
    }

    public function getController()
    {
        return $this->container->get(MockController::class);
    }
}
