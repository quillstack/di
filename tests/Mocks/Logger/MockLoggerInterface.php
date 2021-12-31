<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Mocks\Logger;

interface MockLoggerInterface
{
    public function info(string $message);
}
