<?php

namespace Quillstack\Mocks\DI\ContainerItself;

use Quillstack\DI\Container;
use Quillstack\Mocks\DI\Simple\MockController;

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
