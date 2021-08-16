<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\Loop;

final class MockE
{
    public function __construct(private MockC $mock)
    {
        //
    }
}
