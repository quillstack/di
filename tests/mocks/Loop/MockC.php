<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\Loop;

final class MockC
{
    public function __construct(private MockD $mock)
    {
        //
    }
}
