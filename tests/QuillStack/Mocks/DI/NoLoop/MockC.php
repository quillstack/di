<?php

namespace QuillStack\Mocks\DI\NoLoop;

final class MockC
{
    public function __construct(public MockB $mock, private MockA $mockA)
    {
    }
}
