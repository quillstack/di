<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\NoLoop;

final class MockC
{
    public function __construct(public MockB $mock, private MockA $mockA)
    {
        //
    }
}
