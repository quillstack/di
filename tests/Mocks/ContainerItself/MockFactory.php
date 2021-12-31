<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\ContainerItself;

use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\Simple\MockController;

final class MockFactory
{
    public function __construct(public Container $container)
    {
        //
    }

    public function getController()
    {
        return $this->container->get(MockController::class);
    }
}
