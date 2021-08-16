<?php

namespace QuillStack\Mocks\DI\Simple;

final class MockController
{
    public function __construct(public MockService $service)
    {
    }
}
