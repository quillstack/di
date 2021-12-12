<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Loop;

final class MockB
{
    public function __construct(private MockA $mockA)
    {
        //
    }
}
