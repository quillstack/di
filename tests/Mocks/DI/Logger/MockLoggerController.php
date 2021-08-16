<?php

namespace Quillstack\Mocks\DI\Logger;

final class MockLoggerController
{
    public function __construct(public MockLoggerInterface $logger)
    {
    }
}
