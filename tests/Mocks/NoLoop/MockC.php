<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\NoLoop;

final class MockC
{
    public function __construct(public MockB $mock, private MockA $mockA)
    {
        //
    }
}
