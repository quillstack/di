<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Loop;

final class MockC
{
    public function __construct(private MockD $mock)
    {
        //
    }
}
