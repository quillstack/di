<?php

declare(strict_types=1);

namespace QuillStack\Mocks\DI\Logger;

final class MockLoggerController
{
    /**
     * @var MockLoggerInterface
     */
    public MockLoggerInterface $logger;

    public function __construct(MockLoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
