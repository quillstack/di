<?php

namespace QuillStack\Mocks\DI\Logger;

final class MockLoggerController
{
    public function __construct(public MockLoggerInterface $logger)
    {
    }
}
