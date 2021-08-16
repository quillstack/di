<?php

namespace Quillstack\Mocks\DI\NoLoop;

final class MockA
{
    public function __construct(public MockB $mock)
    {
    }
}
