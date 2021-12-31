<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Logger;

final class MockLoggerController
{
    public function __construct(public MockLoggerInterface $logger)
    {
        //
    }
}
