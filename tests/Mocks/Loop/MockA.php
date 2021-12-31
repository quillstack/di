<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Loop;

final class MockA
{
    public function __construct(private MockB $mockB)
    {
        //
    }
}
