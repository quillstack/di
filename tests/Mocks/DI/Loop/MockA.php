<?php

namespace Quillstack\Mocks\DI\Loop;

final class MockA
{
    public function __construct(private MockB $mockB)
    {
    }
}
