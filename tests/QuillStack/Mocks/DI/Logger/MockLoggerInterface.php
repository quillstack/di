<?php

declare(strict_types=1);

namespace QuillStack\Mocks\DI\Logger;

interface MockLoggerInterface
{
    public function info(string $message);
}
