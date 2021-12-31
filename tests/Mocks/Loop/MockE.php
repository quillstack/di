<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Loop;

final class MockE
{
    public function __construct(private MockC $mock)
    {
        //
    }
}
