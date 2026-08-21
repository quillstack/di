<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\NestedConfig;

final class MockInnerService
{
    public function __construct(public readonly string $name = 'inner default')
    {
        //
    }
}
