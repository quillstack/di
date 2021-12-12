<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\NoLoop;

final class MockA
{
    public function __construct(public MockB $mock)
    {
        //
    }
}
