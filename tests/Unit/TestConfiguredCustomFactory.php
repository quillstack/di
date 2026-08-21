<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\External\ExternalClassInterface;
use Quillstack\DI\Tests\Mocks\External\ExternalController;
use Quillstack\DI\Tests\Mocks\External\ExternalInstanceFactory;
use Quillstack\UnitTests\AssertEqual;

/**
 * A custom factory used to be built with a plain `new`, which left it with no configuration
 * and no dependencies: it could not be told anything about what it was supposed to build.
 */
class TestConfiguredCustomFactory
{
    public function __construct(private AssertEqual $assertEqual)
    {
        //
    }

    public function theFactoryIsConfiguredLikeAnythingElse()
    {
        $container = new Container([
            ExternalClassInterface::class => ExternalInstanceFactory::class,
            ExternalInstanceFactory::class => ['name' => 'configured'],
        ]);

        // Building something through the factory is what builds the factory.
        $controller = $container->get(ExternalController::class);

        $this->assertEqual->equal('test', $controller->external->test);
        $this->assertEqual->equal('configured', $container->get(ExternalInstanceFactory::class)->name);
    }

    public function withoutConfigurationTheFactoryKeepsItsDefaults()
    {
        $container = new Container([ExternalClassInterface::class => ExternalInstanceFactory::class]);
        $container->get(ExternalController::class);

        $this->assertEqual->equal('default', $container->get(ExternalInstanceFactory::class)->name);
    }
}
