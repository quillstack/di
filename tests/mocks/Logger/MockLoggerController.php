<?php

declare(strict_types=1);

namespace Quillstack\Mocks\DI\Logger;

final class MockLoggerController
{
    public function __construct(public MockLoggerInterface $logger)
    {
        //
    }
}
