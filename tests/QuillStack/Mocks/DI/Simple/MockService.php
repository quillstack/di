<?php

namespace QuillStack\Mocks\DI\Simple;

final class MockService
{
    public function __construct(public MockRepository $repository)
    {
    }
}
