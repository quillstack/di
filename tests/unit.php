<?php

declare(strict_types=1);

return [
    \Quillstack\DI\Tests\Unit\TestContainerHelper::class,
    \Quillstack\DI\Tests\Unit\TestContainer::class,
    \Quillstack\DI\Tests\Unit\TestInstanceFactory::class,

    \Quillstack\DI\Tests\Unit\InstanceFactories\TestClassFromInterfaceFactory::class,
    \Quillstack\DI\Tests\Unit\InstanceFactories\TestExternalInstanceFactory::class,
    \Quillstack\DI\Tests\Unit\InstanceFactories\TestInstantiableClassFactory::class,

    \Quillstack\DI\Tests\Unit\TestAddToConfig::class,
];
