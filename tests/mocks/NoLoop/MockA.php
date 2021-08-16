<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\NoLoop;

final class MockA
{
    public function __construct(public MockB $mock)
    {
        //
    }
}
