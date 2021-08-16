<?php

namespace QuillStack\Mocks\DI\Loop;

final class MockC
{
    public function __construct(private MockD $mock)
    {
    }
}
