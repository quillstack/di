<?php

namespace Quillstack\Mocks\DI\Loop;

final class MockB
{
    public function __construct(private MockA $mockA)
    {
    }
}
